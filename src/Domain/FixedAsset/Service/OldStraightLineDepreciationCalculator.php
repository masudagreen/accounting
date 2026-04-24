<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Support\Decimal\Decimal;

/**
 * 旧定額法 (acquired before 2007-04-01).
 *
 * Base = acquisition_cost * (1 - 残存価額率), where 残存価額率 = 10%.
 * Annual depreciation = base / useful_life_years, month-prorated.
 *
 * Once accumulated depreciation reaches 95% of the acquisition cost the
 * asset enters the "5-year average writedown" — the remaining 5% is
 * amortized over 60 months to the memo 1 yen.
 */
final class OldStraightLineDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        // 旧法残存価額 = 取得価額 × 10%
        $legacyResidual = DecimalMath::mulFloor($request->acquisitionCost, 0.10);
        $base = DecimalMath::sub($request->acquisitionCost, $legacyResidual);

        $ulYears = max(1, $request->usefulLifeYears);
        $yearly = DecimalMath::divFloor($base, $ulYears);

        $months = $request->monthsInService;
        $totalMonths = $request->fiscalTermMonths > 0 ? $request->fiscalTermMonths : 12;
        $dep = DecimalMath::mulFloor($yearly, $months / $totalMonths);

        // 95% 上限に達したら 5 年均等
        $survivalLimit = DecimalMath::mulFloor($request->acquisitionCost, 0.05);
        $accumAfter = Decimal::add($request->openingAccumulatedDepreciation, $dep);
        $costMinusLimit = DecimalMath::sub($request->acquisitionCost, $survivalLimit);
        if (Decimal::compare($accumAfter, $costMinusLimit) > 0) {
            // 95% 到達 — スケジュールを均等割へ切替。
            // 残り = closing - memo(1円)
            $fiveYearShare = DecimalMath::divFloor(
                DecimalMath::sub($survivalLimit, '1.0000'),
                60,
            );
            $dep = DecimalMath::mulFloor($fiveYearShare, $months);
        }

        $floor = StraightLineDepreciationCalculator::memoFloor($request);
        $maxAllowed = DecimalMath::sub($request->openingBookValue, $floor);
        if (Decimal::compare($maxAllowed, '0.0000') < 0) {
            $maxAllowed = '0.0000';
        }
        if (Decimal::compare($dep, $maxAllowed) > 0) {
            $dep = $maxAllowed;
        }

        return StraightLineDepreciationCalculator::finalize($request, $dep);
    }
}
