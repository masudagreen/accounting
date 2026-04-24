<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ledger;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Domain\Ledger\LedgerQueryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see LedgerQueryInterface}.
 *
 * Two SQL statements:
 *   1. Account-title metadata query — returns every candidate account
 *      (optionally filtered to a single `$accountTitleId`) so we can emit
 *      empty books for accounts that had zero movement in the period.
 *   2. Journal line projection — joins `journal_entry_lines` to
 *      `journal_entries`, restricts to `posted` / `approved` + not deleted,
 *      and emits one row per line ordered by account code, entry_date,
 *      journal_entry id, line_no.
 *
 * Counter accounts are resolved in-PHP per journal entry by collecting
 * all the OTHER lines of the same entry. When a journal has only one
 * other line the counter is that line's account; otherwise the legacy
 * "諸口" (sundries) sentinel is used, matching the old `idAccountTitleContra = else`
 * path in `Ledger.php::_updateSearch()`.
 *
 * The query returns placeholder 0 opening balances; the use case
 * re-computes the book with actual opening balances via
 * {@see \Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface}.
 */
final class PdoLedgerQueryService implements LedgerQueryInterface
{
    private const ACCOUNT_SQL_ALL = <<<SQL
        SELECT id, code, name, normal_side
        FROM account_titles
        WHERE entity_id = :entity
          AND is_active = 1
        ORDER BY code ASC
        SQL;

    private const ACCOUNT_SQL_ONE = <<<SQL
        SELECT id, code, name, normal_side
        FROM account_titles
        WHERE entity_id = :entity AND id = :account
        LIMIT 1
        SQL;

    private const LINES_SQL = <<<SQL
        SELECT
            jel.id               AS line_id,
            jel.entry_id         AS entry_id,
            jel.line_no          AS line_no,
            jel.side             AS side,
            jel.amount           AS amount,
            jel.memo             AS memo,
            jel.account_title_id AS account_title_id,
            je.journal_date      AS journal_date,
            je.summary           AS summary
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.entry_id
        WHERE je.entity_id      = :entity
          AND je.fiscal_term_id = :term
          AND je.journal_date  >= :from
          AND je.journal_date  <= :to
          AND je.deleted_at IS NULL
          AND je.status IN ('posted', 'approved')
        ORDER BY je.journal_date ASC, je.id ASC, jel.line_no ASC
        SQL;

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function query(
        string $entityId,
        string $fiscalTermId,
        ?string $accountTitleId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): Ledger {
        $accounts = $this->fetchAccounts($entityId, $accountTitleId);
        /** @var array<string, array{code:string, name:string, normal_side:string}> $accountsById */
        $accountsById = [];
        foreach ($accounts as $a) {
            $accountsById[$a['id']] = [
                'code'        => $a['code'],
                'name'        => $a['name'],
                'normal_side' => $a['normal_side'],
            ];
        }

        $allLines = $this->fetchLines($entityId, $fiscalTermId, $from, $to);

        // Group lines by entry so we can resolve the counter account(s).
        /** @var array<string, list<array<string, mixed>>> $byEntry */
        $byEntry = [];
        foreach ($allLines as $line) {
            $byEntry[(string) $line['entry_id']][] = $line;
        }

        /** @var array<string, list<array{
         *     journalEntryId: string,
         *     journalEntryLineId: string,
         *     entryDate: \DateTimeImmutable,
         *     summary: string,
         *     memo: string,
         *     counterAccountCode: string,
         *     counterAccountName: string,
         *     debitAmount: string,
         *     creditAmount: string,
         * }>> $entriesByAccount
         */
        $entriesByAccount = [];

        foreach ($allLines as $line) {
            /** @var string $accountId */
            $accountId = (string) $line['account_title_id'];
            $accountIdEncoded = self::encodeBinId($accountId);
            if ($accountTitleId !== null && $accountIdEncoded !== $accountTitleId) {
                continue;
            }
            if (!isset($accountsById[$accountIdEncoded])) {
                continue;
            }
            $entryId = (string) $line['entry_id'];
            $entryLines = $byEntry[$entryId] ?? [];
            [$counterCode, $counterName] = $this->resolveCounter($accountId, $entryLines, $accountsById);

            $amount = (string) $line['amount'];
            $side = (string) $line['side'];
            $debit = $side === 'debit' ? $amount : '0';
            $credit = $side === 'credit' ? $amount : '0';

            $entryDate = self::parseDate((string) $line['journal_date']);
            $entriesByAccount[$accountIdEncoded] ??= [];
            $entriesByAccount[$accountIdEncoded][] = [
                'journalEntryId'     => self::encodeBinId($entryId),
                'journalEntryLineId' => self::encodeBinId((string) $line['line_id']),
                'entryDate'          => $entryDate,
                'summary'            => (string) $line['summary'],
                'memo'               => (string) $line['memo'],
                'counterAccountCode' => $counterCode,
                'counterAccountName' => $counterName,
                'debitAmount'        => $debit,
                'creditAmount'       => $credit,
            ];
        }

        $books = [];
        foreach ($accounts as $a) {
            $id = $a['id'];
            if ($accountTitleId !== null && $id !== $accountTitleId) {
                continue;
            }
            $raw = $entriesByAccount[$id] ?? [];
            // Use compute() with openingBalance=0 — the use case recomputes
            // the book with the real opening balance from the repository.
            $books[] = LedgerBook::compute(
                accountTitleId: $id,
                accountTitleCode: $a['code'],
                accountTitleName: $a['name'],
                normalSide: $a['normal_side'],
                openingBalance: '0',
                rawEntries: $raw,
            );
        }

        return new Ledger(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $from,
            toDate: $to,
            currencyCode: 'JPY',
            books: $books,
            generatedAt: new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }

    /**
     * @return list<array{id:string, code:string, name:string, normal_side:string}>
     */
    private function fetchAccounts(string $entityId, ?string $accountTitleId): array
    {
        if ($accountTitleId !== null) {
            $stmt = $this->pdo->prepare(self::ACCOUNT_SQL_ONE);
            $stmt->execute([
                ':entity'  => UlidGenerator::decode($entityId),
                ':account' => UlidGenerator::decode($accountTitleId),
            ]);
        } else {
            $stmt = $this->pdo->prepare(self::ACCOUNT_SQL_ALL);
            $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        }
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'          => self::encodeBinId((string) ($r['id'] ?? '')),
                'code'        => (string) ($r['code'] ?? ''),
                'name'        => (string) ($r['name'] ?? ''),
                'normal_side' => (string) ($r['normal_side'] ?? LedgerBook::NORMAL_DEBIT),
            ];
        }
        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchLines(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array {
        $stmt = $this->pdo->prepare(self::LINES_SQL);
        $stmt->execute([
            ':entity' => UlidGenerator::decode($entityId),
            ':term'   => UlidGenerator::decode($fiscalTermId),
            ':from'   => $from->format('Y-m-d'),
            ':to'     => $to->format('Y-m-d'),
        ]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $entryLines
     * @param array<string, array{code:string, name:string, normal_side:string}> $accountsById
     * @return array{0:string, 1:string}
     */
    private function resolveCounter(
        string $subjectAccountBinaryId,
        array $entryLines,
        array $accountsById,
    ): array {
        $others = [];
        foreach ($entryLines as $l) {
            if ((string) $l['account_title_id'] === $subjectAccountBinaryId) {
                continue;
            }
            $others[] = $l;
        }
        if (count($others) === 1) {
            $otherId = self::encodeBinId((string) $others[0]['account_title_id']);
            $meta = $accountsById[$otherId] ?? null;
            if ($meta !== null) {
                return [$meta['code'], $meta['name']];
            }
            return ['', ''];
        }
        if (count($others) === 0) {
            return ['', ''];
        }
        return ['', LedgerEntry::COUNTER_SUNDRIES];
    }

    private static function encodeBinId(string $raw): string
    {
        if ($raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function parseDate(string $raw): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return new DateTimeImmutable('1970-01-01', new DateTimeZone('UTC'));
        }
    }
}
