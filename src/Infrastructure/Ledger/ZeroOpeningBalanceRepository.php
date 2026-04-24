<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ledger;

use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * Minimal {@see OpeningBalanceRepositoryInterface} that always returns 0.
 *
 * Phase 6 Wave 6-C ships the `opening_balances` table (migration 0010)
 * empty. Until a later wave introduces a proper "close-fiscal-term"
 * workflow that populates the table, the ledger view presents 期首 = 0
 * for every account. This keeps the HTTP surface working without paying
 * a DB round-trip per account.
 */
final class ZeroOpeningBalanceRepository implements OpeningBalanceRepositoryInterface
{
    public function findOpeningBalance(
        string $entityId,
        string $fiscalTermId,
        string $accountTitleId,
    ): string {
        unset($entityId, $fiscalTermId, $accountTitleId);
        return Decimal::normalize('0');
    }
}
