<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

/**
 * Output of a single-period depreciation computation.
 *
 * `depreciationAmount` never exceeds `request.openingBookValue - residualValue - 1円`
 * (memo-retain rule). All fields are DECIMAL(18,4) strings.
 */
final readonly class DepreciationCalculationResult
{
    public function __construct(
        public string $depreciationAmount,
        public string $accumulatedDepreciation,
        public string $closingBookValue,
    ) {
    }
}
