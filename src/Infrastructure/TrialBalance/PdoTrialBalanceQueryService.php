<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see TrialBalanceQueryInterface}.
 *
 * `queryByPeriod()` issues a single grouped SUM over
 * `journal_entry_lines` joined to `journal_entries` and `account_titles`,
 * only counting rows whose parent entry is `posted` or `approved` and not
 * soft-deleted.
 *
 * Performance target (ADR-007 §9.5 Wave 1): 10 万明細で < 2s.
 * The query relies on `idx_journal__alive` + `idx_journal_lines__account_booked`,
 * both created by migration 0003. A manual EXPLAIN run during development
 * confirmed index-merge on the composite key plus a group-by on the
 * already-indexed account_title_id.
 */
final class PdoTrialBalanceQueryService implements TrialBalanceQueryInterface
{
    private const SUM_SQL = <<<SQL
        SELECT
            at.id              AS account_id,
            at.code            AS account_code,
            at.name            AS account_name,
            at.category        AS account_category,
            at.normal_side     AS normal_side,
            SUM(CASE WHEN jel.side = 'debit'  THEN jel.amount ELSE 0 END) AS debit_total,
            SUM(CASE WHEN jel.side = 'credit' THEN jel.amount ELSE 0 END) AS credit_total,
            COUNT(jel.id)      AS line_count
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.entry_id
        JOIN account_titles at  ON at.id = jel.account_title_id
        WHERE je.entity_id       = :entity
          AND je.fiscal_term_id  = :term
          AND je.journal_date   >= :from
          AND je.journal_date   <= :to
          AND je.deleted_at IS NULL
          AND je.status IN ('posted', 'approved')
        GROUP BY at.id, at.code, at.name, at.category, at.normal_side
        ORDER BY at.code ASC
        SQL;

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function queryByPeriod(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): TrialBalance {
        $stmt = $this->pdo->prepare(self::SUM_SQL);
        $stmt->execute([
            ':entity' => UlidGenerator::decode($entityId),
            ':term'   => UlidGenerator::decode($fiscalTermId),
            ':from'   => $from->format('Y-m-d'),
            ':to'     => $to->format('Y-m-d'),
        ]);
        /** @var list<array<string, mixed>> $rawRows */
        $rawRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $rows = [];
        foreach ($rawRows as $r) {
            $rows[] = TrialBalanceRow::compute(
                accountTitleId: self::stringifyId($r['account_id'] ?? ''),
                accountTitleCode: (string) ($r['account_code'] ?? ''),
                accountTitleName: (string) ($r['account_name'] ?? ''),
                accountCategory: (string) ($r['account_category'] ?? ''),
                normalSide: (string) ($r['normal_side'] ?? TrialBalanceRow::NORMAL_DEBIT),
                debitTotal: (string) ($r['debit_total'] ?? '0'),
                creditTotal: (string) ($r['credit_total'] ?? '0'),
                lineCount: (int) ($r['line_count'] ?? 0),
            );
        }

        return new TrialBalance(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $from,
            toDate: $to,
            currencyCode: 'JPY',
            rows: $rows,
            generatedAt: new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }

    public function latestSnapshotDate(string $entityId, string $fiscalTermId): ?DateTimeImmutable
    {
        $stmt = $this->pdo->prepare(
            'SELECT MAX(snapshot_date) AS latest
             FROM trial_balance_snapshots
             WHERE entity_id = :entity AND fiscal_term_id = :term',
        );
        $stmt->execute([
            ':entity' => UlidGenerator::decode($entityId),
            ':term'   => UlidGenerator::decode($fiscalTermId),
        ]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || $raw === null || !is_string($raw) || $raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }
}
