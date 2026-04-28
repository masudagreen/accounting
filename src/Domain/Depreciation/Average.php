<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 平均償却 (Average) - 月別均等.
 *
 * 計算式:
 *   月額 = 取得価額 / (耐用年数 × 12)
 *   当期償却 = 月額 × 期内使用月数 × 事業供用割合
 */
final class Average
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $roundingMode,
    ): DepreciationResult {
        $monthsUsed = self::monthsUsedInPeriod($acquisition, $period);
        if ($monthsUsed === 0) {
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $acquisition->cost()->minus($previousAccumulated),
                monthsUsedInPeriod: 0,
            );
        }

        $totalMonths = $acquisition->usefulLifeYears() * 12;
        $brickMode = self::brickMode($roundingMode);

        // 月額 (BigDecimal で除算)
        $monthlyBd = $acquisition->cost()->toBigDecimal()
            ->dividedBy(BigDecimal::of($totalMonths), 10, BrickRoundingMode::HALF_UP);

        $depBd = $monthlyBd->multipliedBy(BigDecimal::of($monthsUsed));

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
