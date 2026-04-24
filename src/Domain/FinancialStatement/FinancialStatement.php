<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement;

use DateTimeImmutable;

/**
 * Read model for a period-bounded set of financial statements (BS, PL, CS).
 *
 * Built once by {@see \Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase}
 * and handed to the HTTP / serializer layer unchanged. All child objects are
 * readonly; no caller mutates this aggregate after construction.
 *
 * `bs`, `pl`, `cs` may each be `null` when the requested kind filters them
 * out. `totals` holds aggregated figures for quick renderer access
 * (e.g. `net_income`, `total_assets`).
 */
final readonly class FinancialStatement
{
    /**
     * @param array<string, Section> $bs  Keyed by section code (assets/liabilities/equity)
     * @param array<string, Section> $pl  Keyed by section code (revenue/expenses)
     * @param array<string, Section> $cs  Keyed by section code (operating/investing/financing)
     * @param array<string, string>  $totals Scale-4 aggregated decimal totals
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public FinancialStatementKind $kind,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode,
        public array $bs,
        public array $pl,
        public array $cs,
        public array $totals,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    public function hasBalanceSheet(): bool
    {
        return $this->bs !== [];
    }

    public function hasProfitAndLoss(): bool
    {
        return $this->pl !== [];
    }

    public function hasCashFlow(): bool
    {
        return $this->cs !== [];
    }
}
