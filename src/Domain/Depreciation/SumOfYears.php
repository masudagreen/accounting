<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 級数法 (Sum-of-years-digits).
 *
 * 計算式:
 *   級数和 N = 1 + 2 + ... + usefulLifeYears = usefulLifeYears × (usefulLifeYears + 1) / 2
 *   年度n の償却率 = (usefulLifeYears - n + 1) / N
 *   当期償却 = 取得価額 × 償却率 × 月按分 × 事業供用割合
 *
 * yearIndex は 1 始まり (1年目=1).
 */
final class SumOfYears
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        int $yearIndex,
        RoundingMode $roundingMode,
    ): DepreciationResult {
        if ($yearIndex < 1) {
            throw new \InvalidArgumentException('yearIndex must be >= 1');
        }
        if ($yearIndex > $acquisition->usefulLifeYears()) {
            // 耐用年数を超えた → 0
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $acquisition->cost()->minus($previousAccumulated),
                monthsUsedInPeriod: $period->termMonths(),
            );
        }

        $monthsUsed = self::monthsUsedInPeriod($acquisition, $period);
        if ($monthsUsed === 0) {
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $acquisition->cost()->minus($previousAccumulated),
                monthsUsedInPeriod: 0,
            );
        }

        $n = $acquisition->usefulLifeYears();
        $sumDigits = ($n * ($n + 1)) / 2;
        $numerator = $n - $yearIndex + 1;

        $brickMode = self::brickMode($roundingMode);
        $termMonths = BigDecimal::of($period->termMonths());

        $depBd = $acquisition->cost()->toBigDecimal()
            ->multipliedBy(BigDecimal::of($numerator))
            ->dividedBy(BigDecimal::of($sumDigits), 10, BrickRoundingMode::HALF_UP)
            ->multipliedBy(BigDecimal::of($monthsUsed))
            ->dividedBy($termMonths, 10, BrickRoundingMode::HALF_UP);

        if ($acquisition->businessUseRatioPercent() !== 100) {
            $depBd = $depBd
                ->multipliedBy(BigDecimal::of($acquisition->businessUseRatioPercent()))
                ->dividedBy(BigDecimal::of('100'), 10, BrickRoundingMode::HALF_UP);
        }

        $dep = Money::ofYen($depBd->toScale(0, $brickMode));

        $maxDep = $acquisition->cost()->minus($previousAccumulated)->minus(Money::ofYen(1));
        if ($maxDep->isNegative()) {
            $dep = Money::zero();
        } elseif ($dep->isGreaterThan($maxDep)) {
            $dep = $maxDep;
        }

        $accumulated = $previousAccumulated->plus($dep);
        return new DepreciationResult(
            depreciation: $dep,
            accumulatedClosing: $accumulated,
            bookValueClosing: $acquisition->cost()->minus($accumulated),
            monthsUsedInPeriod: $monthsUsed,
        );
    }

    private static function monthsUsedInPeriod(
        Acquisition $acquisition,
        FiscalPeriod $period,
    ): int {
        if ($acquisition->acquisitionDate() > $period->endDate()) {
            return 0;
        }
        if ($period->contains($acquisition->acquisitionDate())) {
            return $period->monthsRemaining($acquisition->acquisitionDate());
        }
        return $period->termMonths();
    }

    private static function brickMode(RoundingMode $mode): BrickRoundingMode
    {
        return match ($mode) {
            RoundingMode::Floor => BrickRoundingMode::FLOOR,
            RoundingMode::Ceil  => BrickRoundingMode::CEILING,
            RoundingMode::Round => BrickRoundingMode::HALF_UP,
        };
    }
}
