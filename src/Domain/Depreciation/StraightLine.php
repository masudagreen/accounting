<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 定額法 (Straight-line) — 平成19年4月1日以降取得分.
 *
 * 計算式:
 *   年間償却費 = 取得価額 × 償却率
 *   月按分     = 年間償却費 × (期内使用月数 / 期間月数)
 *   事業供用割合反映 = 上記 × (businessUseRatioPercent / 100)
 *   最終調整  = 期末簿価が 1 円を下回らないよう調整
 */
final class StraightLine
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $roundingMode,
    ): DepreciationResult {
        $monthsUsed = self::monthsUsedInPeriod($acquisition, $period);
        if ($monthsUsed === 0) {
            // 期外取得 / 既に除却 → 当期償却なし
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $acquisition->cost()->minus($previousAccumulated),
                monthsUsedInPeriod: 0,
            );
        }

        $rate = DepreciationRateTable::straightLineNew($acquisition->usefulLifeYears());
        $brickMode = self::brickMode($roundingMode);

        // 年間償却費 (満期 = 期間月数全期使用相当)
        $annual = $acquisition->cost()->toBigDecimal()->multipliedBy($rate);

        // 月按分
        $depBd = $annual
            ->multipliedBy(BigDecimal::of($monthsUsed))
            ->dividedBy(BigDecimal::of($period->termMonths()), 10, BrickRoundingMode::HALF_UP);

        // 事業供用割合
        if ($acquisition->businessUseRatioPercent() !== 100) {
            $depBd = $depBd
                ->multipliedBy(BigDecimal::of($acquisition->businessUseRatioPercent()))
                ->dividedBy(BigDecimal::of('100'), 10, BrickRoundingMode::HALF_UP);
        }

        // 端数処理 (整数円)
        $dep = Money::ofYen($depBd->toScale(0, $brickMode));

        // 1円残価で頭打ち: 期末簿価 = cost - (prevAccumulated + dep) >= 1
        $maxDep = $acquisition->cost()
            ->minus($previousAccumulated)
            ->minus(Money::ofYen(1));

        if ($maxDep->isNegative()) {
            // 既に簿価1円以下まで償却済
            $dep = Money::zero();
        } elseif ($dep->isGreaterThan($maxDep)) {
            $dep = $maxDep;
        }

        $accumulatedClosing = $previousAccumulated->plus($dep);
        $bookValueClosing = $acquisition->cost()->minus($accumulatedClosing);

        return new DepreciationResult(
            depreciation: $dep,
            accumulatedClosing: $accumulatedClosing,
            bookValueClosing: $bookValueClosing,
            monthsUsedInPeriod: $monthsUsed,
        );
    }

    /**
     * 当期内の使用月数を返す.
     * 取得日が期内: 取得月から期末月まで (満1ヶ月計上)
     * 取得日が期外 (過去): 期間全体
     */
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
        // 期外 (過去) 取得 → 期間全体
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
