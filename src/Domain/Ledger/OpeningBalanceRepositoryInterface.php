<?php

declare(strict_types=1);

namespace Rucaro\Domain\Ledger;

/**
 * Port for per-account opening-balance lookup (期首残高).
 *
 * Ledger views need to display the "前期繰越" amount at the top of each
 * account book, and balance-sheet accounts also roll that balance forward
 * in the running total for each subsequent entry. The actual value is
 * sourced from an `opening_balances` table that is populated when a
 * fiscal term is closed; during Wave 6-C the table is seeded empty and
 * the infrastructure implementation simply returns 0 for every account.
 *
 * The Decimal returned follows {@see \Rucaro\Support\Decimal\Decimal::SCALE}
 * (scale-4 string form).
 */
interface OpeningBalanceRepositoryInterface
{
    /**
     * @return string Scale-4 decimal; "0.0000" when no row exists.
     */
    public function findOpeningBalance(
        string $entityId,
        string $fiscalTermId,
        string $accountTitleId,
    ): string;
}
