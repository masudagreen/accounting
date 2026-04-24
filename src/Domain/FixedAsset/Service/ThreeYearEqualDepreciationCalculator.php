<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Support\Decimal\Decimal;

/**
 * 一括償却資産（3 年均等） - depreciate the full acquisition cost over
 * exactly 3 fiscal terms regardless of service date.
 */
final class ThreeYearEqualDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        $yearly = DecimalMath::divFloor($request->acquisitionCost, 3);
        $dep = $yearly;
        // Last period absorbs the remainder so three periods sum to
        // exactly acquisition_cost - no residual for this regime.
        if ($request->periodNumber >= 3) {
            $dep = $request->openingBookValue;
        }
        // Cap at opening book value.
        if (Decimal::compare($dep, $request->openingBookValue) > 0) {
            $dep = $request->openingBookValue;
        }
        return StraightLineDepreciationCalculator::finalize($request, $dep);
    }
}
