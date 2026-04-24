<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

/**
 * Pass-through calculator for non-depreciable assets (land, in-progress
 * construction, etc.). Always emits 0 depreciation.
 */
final class NoDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        return StraightLineDepreciationCalculator::finalize($request, '0.0000');
    }
}
