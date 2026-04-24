<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use DateTimeImmutable;

/**
 * Immutable input for one period's depreciation computation.
 *
 * All decimal inputs are DECIMAL(18,4) strings. Periods are expressed as
 * (periodNumber, periodStart, periodEnd) so callers control the fiscal
 * term window — a partial first period (e.g. acquired mid-year) is handled
 * via `monthsInService`.
 */
final readonly class DepreciationCalculationRequest
{
    public function __construct(
        public string $acquisitionCost,
        public string $residualValue,
        public int $usefulLifeYears,
        public DateTimeImmutable $serviceStartDate,
        public DateTimeImmutable $periodStartDate,
        public DateTimeImmutable $periodEndDate,
        public int $periodNumber,
        public int $monthsInService,
        public int $fiscalTermMonths,
        public string $openingBookValue,
        public string $openingAccumulatedDepreciation,
    ) {
    }
}
