<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

use DateTimeImmutable;

/**
 * Port for building a {@see Ledger} read model directly from the
 * Journal tables. Implementations live in the Infrastructure layer.
 *
 * The query must:
 *   - Restrict to `status IN ('posted', 'approved')` and `deleted_at IS NULL`.
 *   - Filter to the given (entity, fiscal term) and `journal_date ∈ [$from, $to]`.
 *   - Optionally filter to a single account title when `$accountTitleId` is non-null.
 *   - Return books sorted by account code ASC; entries within a book sorted by
 *     journal_date ASC, then journal_entry_id ASC, then line_no ASC.
 *
 * The use case supplies opening balances via
 * {@see OpeningBalanceRepositoryInterface} — the query does not need to
 * reason about them.
 */
interface LedgerQueryInterface
{
    public function query(
        string $entityId,
        string $fiscalTermId,
        ?string $accountTitleId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): Ledger;
}
