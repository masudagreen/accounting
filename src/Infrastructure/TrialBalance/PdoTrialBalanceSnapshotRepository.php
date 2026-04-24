<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see TrialBalanceSnapshotRepositoryInterface}.
 *
 * Writes go through a single transaction so a partial batch never leaves the
 * snapshot table in an inconsistent state (e.g. some accounts refreshed, some
 * carrying stale numbers for the same month).
 */
final class PdoTrialBalanceSnapshotRepository implements TrialBalanceSnapshotRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function saveAll(array $snapshots): void
    {
        if ($snapshots === []) {
            return;
        }
        $needsTransaction = !$this->pdo->inTransaction();
        if ($needsTransaction) {
            $this->pdo->beginTransaction();
        }
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO trial_balance_snapshots (
                    id, entity_id, fiscal_term_id, snapshot_date, account_title_id,
                    debit_total, credit_total, balance, line_count, generated_at
                 ) VALUES (
                    :id, :entity_id, :fiscal_term_id, :snapshot_date, :account_title_id,
                    :debit_total, :credit_total, :balance, :line_count, :generated_at
                 )',
            );
            foreach ($snapshots as $s) {
                $stmt->execute([
                    ':id'               => UlidGenerator::decode($s->id),
                    ':entity_id'        => UlidGenerator::decode($s->entityId),
                    ':fiscal_term_id'   => UlidGenerator::decode($s->fiscalTermId),
                    ':snapshot_date'    => $s->snapshotDate->format('Y-m-d'),
                    ':account_title_id' => UlidGenerator::decode($s->accountTitleId),
                    ':debit_total'      => $s->debitTotal,
                    ':credit_total'     => $s->creditTotal,
                    ':balance'          => $s->balance,
                    ':line_count'       => $s->lineCount,
                    ':generated_at'     => self::fmtTs($s->generatedAt),
                ]);
            }
            if ($needsTransaction) {
                $this->pdo->commit();
            }
        } catch (\Throwable $e) {
            if ($needsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function deleteByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): void {
        $stmt = $this->pdo->prepare(
            'DELETE FROM trial_balance_snapshots
             WHERE entity_id = :entity_id
               AND fiscal_term_id = :fiscal_term_id
               AND snapshot_date = :snapshot_date',
        );
        $stmt->execute([
            ':entity_id'      => UlidGenerator::decode($entityId),
            ':fiscal_term_id' => UlidGenerator::decode($fiscalTermId),
            ':snapshot_date'  => $monthEnd->format('Y-m-d'),
        ]);
    }

    public function findByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): array {
        $stmt = $this->pdo->prepare(
            'SELECT id, entity_id, fiscal_term_id, snapshot_date, account_title_id,
                    debit_total, credit_total, balance, line_count, generated_at
             FROM trial_balance_snapshots
             WHERE entity_id = :entity_id
               AND fiscal_term_id = :fiscal_term_id
               AND snapshot_date = :snapshot_date
             ORDER BY account_title_id ASC',
        );
        $stmt->execute([
            ':entity_id'      => UlidGenerator::decode($entityId),
            ':fiscal_term_id' => UlidGenerator::decode($fiscalTermId),
            ':snapshot_date'  => $monthEnd->format('Y-m-d'),
        ]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = [];
        foreach ($rows as $r) {
            $out[] = new TrialBalanceSnapshot(
                id: self::stringifyId($r['id'] ?? ''),
                entityId: self::stringifyId($r['entity_id'] ?? ''),
                fiscalTermId: self::stringifyId($r['fiscal_term_id'] ?? ''),
                snapshotDate: self::parseDate($r['snapshot_date'] ?? null) ?? $monthEnd,
                accountTitleId: self::stringifyId($r['account_title_id'] ?? ''),
                debitTotal: (string) ($r['debit_total'] ?? '0.0000'),
                creditTotal: (string) ($r['credit_total'] ?? '0.0000'),
                balance: (string) ($r['balance'] ?? '0.0000'),
                lineCount: (int) ($r['line_count'] ?? 0),
                generatedAt: self::parseTs($r['generated_at'] ?? null)
                    ?? new DateTimeImmutable('now', new DateTimeZone('UTC')),
            );
        }
        return $out;
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function parseDate(mixed $raw): ?DateTimeImmutable
    {
        if (!is_string($raw) || $raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function parseTs(mixed $raw): ?DateTimeImmutable
    {
        if (!is_string($raw) || $raw === '') {
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
