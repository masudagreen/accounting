<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Support\Decimal\Decimal;

/**
 * 即時償却 (one-shot write-off) — 30 万円未満の少額減価償却資産特例相当。
 *
 * First-period depreciation = opening_book_value - residual_value.
 * Subsequent periods: zero (book fully depreciated).
 */
final class OneShotDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        $floor = StraightLineDepreciationCalculator::memoFloor($request);
        $dep = DecimalMath::sub($request->openingBookValue, $floor);
        if (Decimal::compare($dep, '0.0000') < 0) {
            $dep = '0.0000';
        }
        return StraightLineDepreciationCalculator::finalize($request, $dep);
    }
}
