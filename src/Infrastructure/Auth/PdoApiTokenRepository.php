<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Auth;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\Auth\ApiToken;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoApiTokenRepository implements ApiTokenRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(ApiToken $token): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO api_tokens (
                id, user_id, token_hash, token_prefix, scopes,
                issued_at, expires_at, revoked_at, last_used_at, created_at, updated_at
             ) VALUES (
                :id, :user_id, :token_hash, :token_prefix, :scopes,
                :issued_at, :expires_at, :revoked_at, :last_used_at, :created_at, :updated_at
             )',
        );
        $stmt->execute([
            ':id' => UlidGenerator::decode($token->id),
            ':user_id' => UlidGenerator::decode($token->userId),
            ':token_hash' => $token->tokenHash,
            ':token_prefix' => $token->tokenPrefix,
            ':scopes' => $token->scopes,
            ':issued_at' => self::fmtTs($token->issuedAt),
            ':expires_at' => self::fmtTs($token->expiresAt),
            ':revoked_at' => $token->revokedAt !== null ? self::fmtTs($token->revokedAt) : null,
            ':last_used_at' => $token->lastUsedAt !== null ? self::fmtTs($token->lastUsedAt) : null,
            ':created_at' => self::fmtTs($token->createdAt),
            ':updated_at' => self::fmtTs($token->updatedAt),
        ]);
    }

    public function findByHash(string $tokenHash): ?ApiToken
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, token_hash, token_prefix, scopes,
                    issued_at, expires_at, revoked_at, last_used_at, created_at, updated_at
             FROM api_tokens
             WHERE token_hash = :h
             LIMIT 1',
        );
        $stmt->execute([':h' => $tokenHash]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $this->hydrate($row);
    }

    public function touchLastUsed(string $id, DateTimeImmutable $at): void
    {
        $stmt = $this->pdo->prepare('UPDATE api_tokens SET last_used_at = :at WHERE id = :id');
        $stmt->execute([
            ':at' => self::fmtTs($at),
            ':id' => UlidGenerator::decode($id),
        ]);
    }

    public function revoke(string $id, DateTimeImmutable $at): void
    {
        $stmt = $this->pdo->prepare('UPDATE api_tokens SET revoked_at = :at WHERE id = :id');
        $stmt->execute([
            ':at' => self::fmtTs($at),
            ':id' => UlidGenerator::decode($id),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ApiToken
    {
        return new ApiToken(
            id: self::stringifyId($row['id'] ?? ''),
            userId: self::stringifyId($row['user_id'] ?? ''),
            tokenHash: (string) ($row['token_hash'] ?? ''),
            tokenPrefix: (string) ($row['token_prefix'] ?? ''),
            scopes: (string) ($row['scopes'] ?? ''),
            issuedAt: self::parseTimestamp($row['issued_at'] ?? null) ?? new DateTimeImmutable('@0'),
            expiresAt: self::parseTimestamp($row['expires_at'] ?? null) ?? new DateTimeImmutable('@0'),
            revokedAt: self::parseTimestamp($row['revoked_at'] ?? null),
            lastUsedAt: self::parseTimestamp($row['last_used_at'] ?? null),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
        );
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
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
