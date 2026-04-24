<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed repository for {@see ApprovalToken} (table: `approval_tokens`).
 *
 * Same shape as {@see \Rucaro\Infrastructure\Auth\PdoApiTokenRepository}:
 * BINARY(16) ids are decoded to/from Crockford Base32 at the boundary, so
 * the rest of the codebase only sees 26-char ULIDs.
 *
 * Schema references:
 *   - 0004_create_receipts_and_approvals.sql (initial table)
 *   - 0007_extend_approval_tokens.sql        (token_prefix / target_kind /
 *                                             issued_by_user_id columns)
 */
final class PdoApprovalTokenRepository implements ApprovalTokenRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(ApprovalToken $token): void
    {
        $existing = $this->findByTokenHash($token->tokenHash);
        if ($existing === null) {
            $this->insert($token);
            return;
        }
        $this->update($token);
    }

    public function findByTokenHash(string $tokenHash): ?ApprovalToken
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, journal_entry_id, receipt_id, target_kind, token_hash, token_prefix,
                    channel, recipient, issued_by_user_id,
                    issued_at, expires_at, responded_at, response, response_detail,
                    created_at, updated_at
               FROM approval_tokens
              WHERE token_hash = :h
              LIMIT 1',
        );
        $stmt->execute([':h' => $tokenHash]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function findByPrefix(string $tokenPrefix): ?ApprovalToken
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, journal_entry_id, receipt_id, target_kind, token_hash, token_prefix,
                    channel, recipient, issued_by_user_id,
                    issued_at, expires_at, responded_at, response, response_detail,
                    created_at, updated_at
               FROM approval_tokens
              WHERE token_prefix = :p
              ORDER BY issued_at DESC
              LIMIT 1',
        );
        $stmt->execute([':p' => $tokenPrefix]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function expirePastDue(DateTimeImmutable $now): int
    {
        // No-op row-level update — the domain treats "past expires_at without
        // response" as expired already. To surface an actionable count we use
        // a SELECT + UPDATE on updated_at so the monitoring job can report.
        $select = $this->pdo->prepare(
            'SELECT id FROM approval_tokens
              WHERE responded_at IS NULL
                AND expires_at <= :now',
        );
        $select->execute([':now' => self::fmtTs($now)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $select->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return 0;
        }
        $update = $this->pdo->prepare('UPDATE approval_tokens SET updated_at = :now WHERE id = :id');
        $count = 0;
        foreach ($rows as $row) {
            $idRaw = $row['id'] ?? '';
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $update->execute([':now' => self::fmtTs($now), ':id' => $idRaw]);
            $count += 1;
        }
        return $count;
    }

    private function insert(ApprovalToken $token): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO approval_tokens (
                id, journal_entry_id, receipt_id, target_kind, token_hash, token_prefix,
                channel, recipient, issued_by_user_id,
                issued_at, expires_at, responded_at, response, response_detail,
                created_at, updated_at
             ) VALUES (
                :id, :journal_entry_id, :receipt_id, :target_kind, :token_hash, :token_prefix,
                :channel, :recipient, :issued_by_user_id,
                :issued_at, :expires_at, :responded_at, :response, :response_detail,
                :created_at, :updated_at
             )',
        );
        $stmt->execute([
            ':id' => UlidGenerator::decode($token->id),
            ':journal_entry_id' => self::resolveBinary($token->targetKind, ApprovalTargetKind::Journal, $token->targetId),
            ':receipt_id' => self::resolveBinary($token->targetKind, ApprovalTargetKind::Receipt, $token->targetId),
            ':target_kind' => $token->targetKind->value,
            ':token_hash' => $token->tokenHash,
            ':token_prefix' => $token->tokenPrefix,
            ':channel' => $token->channel->value,
            ':recipient' => $token->recipient,
            ':issued_by_user_id' => $token->issuedByUserId !== '' ? UlidGenerator::decode($token->issuedByUserId) : null,
            ':issued_at' => self::fmtTs($token->issuedAt),
            ':expires_at' => self::fmtTs($token->expiresAt),
            ':responded_at' => $token->respondedAt !== null ? self::fmtTs($token->respondedAt) : null,
            ':response' => $token->decision?->value,
            ':response_detail' => $token->responseDetail,
            ':created_at' => self::fmtTs($token->createdAt),
            ':updated_at' => self::fmtTs($token->updatedAt),
        ]);
    }

    private function update(ApprovalToken $token): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE approval_tokens
                SET responded_at = :responded_at,
                    response = :response,
                    response_detail = :response_detail,
                    updated_at = :updated_at
              WHERE token_hash = :h',
        );
        $stmt->execute([
            ':h' => $token->tokenHash,
            ':responded_at' => $token->respondedAt !== null ? self::fmtTs($token->respondedAt) : null,
            ':response' => $token->decision?->value,
            ':response_detail' => $token->responseDetail,
            ':updated_at' => self::fmtTs($token->updatedAt),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ApprovalToken
    {
        $targetKind = self::parseTargetKind($row['target_kind'] ?? null);
        $targetId = $targetKind === ApprovalTargetKind::Journal
            ? self::stringifyId($row['journal_entry_id'] ?? null)
            : self::stringifyId($row['receipt_id'] ?? null);

        $decisionRaw = $row['response'] ?? null;
        $decision = null;
        if (is_string($decisionRaw) && $decisionRaw !== '') {
            $decision = ApprovalDecision::tryFrom($decisionRaw);
        }

        return new ApprovalToken(
            id: self::stringifyId($row['id'] ?? null),
            targetKind: $targetKind,
            targetId: $targetId,
            tokenHash: self::asString($row['token_hash'] ?? ''),
            tokenPrefix: self::asString($row['token_prefix'] ?? ''),
            channel: self::parseChannel($row['channel'] ?? null),
            recipient: self::asString($row['recipient'] ?? ''),
            issuedAt: self::parseTimestamp($row['issued_at'] ?? null) ?? new DateTimeImmutable('@0'),
            expiresAt: self::parseTimestamp($row['expires_at'] ?? null) ?? new DateTimeImmutable('@0'),
            respondedAt: self::parseTimestamp($row['responded_at'] ?? null),
            decision: $decision,
            responseDetail: self::asString($row['response_detail'] ?? ''),
            issuedByUserId: self::stringifyId($row['issued_by_user_id'] ?? null),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
        );
    }

    private static function parseTargetKind(mixed $raw): ApprovalTargetKind
    {
        if (is_string($raw) && $raw !== '') {
            $parsed = ApprovalTargetKind::tryFrom($raw);
            if ($parsed !== null) {
                return $parsed;
            }
        }
        return ApprovalTargetKind::Journal;
    }

    private static function parseChannel(mixed $raw): ApprovalChannel
    {
        if (is_string($raw) && $raw !== '') {
            $parsed = ApprovalChannel::tryFrom($raw);
            if ($parsed !== null) {
                return $parsed;
            }
        }
        return ApprovalChannel::Null;
    }

    private static function resolveBinary(
        ApprovalTargetKind $actual,
        ApprovalTargetKind $slot,
        string $id,
    ): ?string {
        if ($actual !== $slot || $id === '') {
            return null;
        }
        return UlidGenerator::decode($id);
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function asString(mixed $raw): string
    {
        return is_string($raw) ? $raw : '';
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function fmtTs(DateTimeImmutable $t): string
    {
        return $t->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
    }
}
