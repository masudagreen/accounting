<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint;

use DateTimeImmutable;
use Rucaro\Support\Decimal\Decimal;

/**
 * Read-model for a single Break-Even Point (CVP) analysis.
 *
 * All amounts are scale-4 decimal strings. Ratios (`contributionMarginRate`,
 * `bepRatio`, `safetyMarginRatio`) are also scale-4 strings and represent
 * decimal fractions, NOT percentages — i.e. `0.8750` means 87.5%.
 *
 * Invariant: when `sales == 0` every ratio is `0.0000` so downstream code
 * never divides by zero.
 */
final readonly class BreakEvenPointAnalysis
{
    /**
     * @param list<array{accountTitleId:string, accountTitleCode:string, accountTitleName:string, costType:string, amount:string}> $variableBreakdown
     * @param list<array{accountTitleId:string, accountTitleCode:string, accountTitleName:string, costType:string, amount:string}> $fixedBreakdown
     * @param list<array{accountTitleId:string, accountTitleCode:string, accountTitleName:string, amount:string}> $salesBreakdown
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode,
        public string $sales,
        public string $variableCosts,
        public string $fixedCosts,
        public string $contributionMargin,
        public string $contributionMarginRate,
        public string $bepSales,
        public string $bepRatio,
        public string $safetyMarginRatio,
        public string $operatingProfit,
        public array $salesBreakdown,
        public array $variableBreakdown,
        public array $fixedBreakdown,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    public function isBelowBreakEven(): bool
    {
        return Decimal::compare($this->sales, $this->bepSales) < 0;
    }
}
