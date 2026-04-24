<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Support\Decimal\Decimal;

/**
 * 定額法 (post-2007-04-01 straight line).
 *
 * Annual depreciation = (acquisition_cost - residual_value) / useful_life_years,
 * then pro-rated by `monthsInService / fiscalTermMonths` for partial periods.
 *
 * Final period: the last yen is retained as a memo (残存簿価 1 円) when
 * `residual_value = 0` and the book is otherwise fully depreciated.
 */
final class StraightLineDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        if ($request->usefulLifeYears <= 0) {
            // Treat as one-shot — entire book value becomes depreciation.
            $dep = DecimalMath::sub($request->openingBookValue, $request->residualValue);
            if (Decimal::compare($dep, '0.0000') < 0) {
                $dep = '0.0000';
            }
            return self::finalize($request, $dep);
        }

        $base = DecimalMath::sub($request->acquisitionCost, $request->residualValue);
        // yearly = base / useful_life
        $yearly = DecimalMath::divFloor($base, $request->usefulLifeYears);
        // period share = yearly * monthsInService / fiscalTermMonths
        $months = $request->monthsInService;
        $totalMonths = $request->fiscalTermMonths;
        if ($totalMonths <= 0) {
            $totalMonths = 12;
        }
        $dep = DecimalMath::mulFloor($yearly, $months / $totalMonths);

        // Cap: never push the book below (residual + 1yen memo).
        $floor = self::memoFloor($request);
        $maxAllowed = DecimalMath::sub($request->openingBookValue, $floor);
        if (Decimal::compare($maxAllowed, '0.0000') < 0) {
            $maxAllowed = '0.0000';
        }
        if (Decimal::compare($dep, $maxAllowed) > 0) {
            $dep = $maxAllowed;
        }

        return self::finalize($request, $dep);
    }

    /**
     * 残存簿価の下限: 法的残存価額が 0 ならば「1 円」を下限にする
     * （備忘記帳）。現行法準拠。`residual_value` が明示的に 1 円以上
     * 指定された場合はそれを下限とする。
     */
    public static function memoFloor(DepreciationCalculationRequest $request): string
    {
        if (Decimal::compare($request->residualValue, '0.0000') === 0) {
            return '1.0000';
        }
        return $request->residualValue;
    }

    public static function finalize(
        DepreciationCalculationRequest $request,
        string $dep,
    ): DepreciationCalculationResult {
        $accum = Decimal::add($request->openingAccumulatedDepreciation, $dep);
        $closing = DecimalMath::sub($request->openingBookValue, $dep);
        return new DepreciationCalculationResult(
            depreciationAmount: Decimal::normalize($dep),
            accumulatedDepreciation: Decimal::normalize($accum),
            closingBookValue: Decimal::normalize($closing),
        );
    }
}
