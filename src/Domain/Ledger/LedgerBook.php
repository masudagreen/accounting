<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

use Rucaro\Support\Decimal\Decimal;

/**
 * One general-ledger "book" — the full ledger for a single account title
 * over a period.
 *
 * The aggregate carries:
 *   - Opening balance (前期繰越) at the start of the period.
 *   - Every journal entry projected as a {@see LedgerEntry}, ordered by
 *     entry_date / journal_entry_id / line_no.
 *   - Period totals (debit / credit) computed once at construction and
 *     the closing balance (期末残高) derived from the normal side.
 *
 * Mirrors the output of `Ledger.php` for a single (idAccountTitle) +
 * period combination on the legacy stack.
 */
final readonly class LedgerBook
{
    public const NORMAL_DEBIT = 'debit';
    public const NORMAL_CREDIT = 'credit';

    /**
     * @param list<LedgerEntry> $entries
     */
    public function __construct(
        public string $accountTitleId,
        public string $accountTitleCode,
        public string $accountTitleName,
        public string $normalSide,
        public string $openingBalance,
        public array $entries,
        public string $debitTotal,
        public string $creditTotal,
        public string $closingBalance,
    ) {
    }

    /**
     * Build a book from raw (already date-sorted) journal projections,
     * computing running balances, debit/credit totals and the closing
     * balance from the opening balance and the account's normal side.
     *
     * Each `$rawEntries` row carries the 9 fields of {@see LedgerEntry}
     * except for `runningBalance`, which is derived here.
     *
     * @param list<array{
     *     journalEntryId: string,
     *     journalEntryLineId: string,
     *     entryDate: \DateTimeImmutable,
     *     summary: string,
     *     memo: string,
     *     counterAccountCode: string,
     *     counterAccountName: string,
     *     debitAmount: string,
     *     creditAmount: string,
     * }> $rawEntries
     */
    public static function compute(
        string $accountTitleId,
        string $accountTitleCode,
        string $accountTitleName,
        string $normalSide,
        string $openingBalance,
        array $rawEntries,
    ): self {
        $running = Decimal::normalize($openingBalance);
        $debitTotal = '0.0000';
        $creditTotal = '0.0000';
        $entries = [];
        foreach ($rawEntries as $r) {
            $debit = Decimal::normalize($r['debitAmount']);
            $credit = Decimal::normalize($r['creditAmount']);
            $debitTotal = Decimal::add($debitTotal, $debit);
            $creditTotal = Decimal::add($creditTotal, $credit);

            // debit-normal accounts go up with debit side, down with credit.
            // credit-normal accounts go up with credit side, down with debit.
            if ($normalSide === self::NORMAL_CREDIT) {
                $running = self::subtract(Decimal::add($running, $credit), $debit);
            } else {
                $running = self::subtract(Decimal::add($running, $debit), $credit);
            }

            $entries[] = new LedgerEntry(
                journalEntryId: $r['journalEntryId'],
                journalEntryLineId: $r['journalEntryLineId'],
                entryDate: $r['entryDate'],
                summary: $r['summary'],
                memo: $r['memo'],
                counterAccountCode: $r['counterAccountCode'],
                counterAccountName: $r['counterAccountName'],
                debitAmount: $debit,
                creditAmount: $credit,
                runningBalance: $running,
            );
        }

        return new self(
            accountTitleId: $accountTitleId,
            accountTitleCode: $accountTitleCode,
            accountTitleName: $accountTitleName,
            normalSide: $normalSide,
            openingBalance: Decimal::normalize($openingBalance),
            entries: $entries,
            debitTotal: Decimal::normalize($debitTotal),
            creditTotal: Decimal::normalize($creditTotal),
            closingBalance: $running,
        );
    }

    private static function subtract(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @var string */
            return bcsub($a, $b, Decimal::SCALE);
        }
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . $b);
        return Decimal::add($a, $negated);
    }
}
