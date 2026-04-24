<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

use DateTimeImmutable;

/**
 * One row of a general ledger (総勘定元帳の 1 行).
 *
 * Read model — immutable by construction. Represents a single journal
 * line projected into the ledger view for an account title, with the
 * running balance calculated up to and including this entry.
 *
 * Corresponds to the legacy `accountingLogCalcJpn` row rendered under
 * `Ledger.php::_updateSearch()` — but the Decimal types are kept as
 * scale-4 strings (Decimal::SCALE) for parity with the rest of the new
 * accounting core.
 *
 * `counterAccountCode` / `counterAccountName` summarise the OTHER side
 * of the same journal entry:
 *   - If the entry has exactly one other line, code/name are that line's.
 *   - If the entry has multiple other lines, names are joined with "／"
 *     and the legacy "諸口" (sundries) label is also accepted via
 *     {@see COUNTER_SUNDRIES}.
 */
final readonly class LedgerEntry
{
    public const COUNTER_SUNDRIES = '諸口';

    public function __construct(
        public string $journalEntryId,
        public string $journalEntryLineId,
        public DateTimeImmutable $entryDate,
        public string $summary,
        public string $memo,
        public string $counterAccountCode,
        public string $counterAccountName,
        public string $debitAmount,
        public string $creditAmount,
        public string $runningBalance,
    ) {
    }
}
