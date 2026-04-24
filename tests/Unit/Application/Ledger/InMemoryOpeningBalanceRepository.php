<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Ledger;

use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * In-memory {@see OpeningBalanceRepositoryInterface} that lets tests
 * seed per-account opening balances.
 */
final class InMemoryOpeningBalanceRepository implements OpeningBalanceRepositoryInterface
{
    /** @var array<string, string> */
    private array $byAccount = [];

    public function set(string $accountTitleId, string $amount): void
    {
        $this->byAccount[$accountTitleId] = $amount;
    }

    public function findOpeningBalance(
        string $entityId,
        string $fiscalTermId,
        string $accountTitleId,
    ): string {
        unset($entityId, $fiscalTermId);
        return Decimal::normalize($this->byAccount[$accountTitleId] ?? '0');
    }
}
