<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 定率法 (Declining-balance) — 平成19年4月1日以降取得分.
 *
 * 計算式:
 *   通常時:  当期償却 = 期首簿価 × 償却率
 *   保証額: 当期償却 < 取得価額 × 償却保証率 となった年度から
 *           改定償却率で残額を均等償却 (以降は毎期同額)
 *   月按分: 期中取得・除却の場合は使用月数で按分
 *   1円残: 期末簿価 >= 1 円 となるよう調整
 *
 * 200%定率法と250%定率法は耐用年数別の表が異なる (DepreciationRateTable参照).
 */
final class DecliningBalance
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        DecliningMethod $method,
        RoundingMode $roundingMode,
    ): DepreciationResult {
        $monthsUsed = self::monthsUsedInPeriod($acquisition, $period);
        $bookValueOpening = $acquisition->cost()->minus($previousAccumulated);

        if ($monthsUsed === 0) {
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $bookValueOpening,
                monthsUsedInPeriod: 0,
            );
        }

        // 既に1円残価以下なら当期0
        if ($bookValueOpening->isLessThanOrEqualTo(Money::ofYen(1))) {
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: $bookValueOpening,
                monthsUsedInPeriod: $monthsUsed,
            );
        }

        $rates = match ($method) {
            DecliningMethod::TwoHundredPercent       => DepreciationRateTable::declining200($acquisition->usefulLifeYears()),
            DecliningMethod::TwoHundredFiftyPercent  => DepreciationRateTable::declining250($acquisition->usefulLifeYears()),
        };

        $brickMode = self::brickMode($roundingMode);
        $termMonths = BigDecimal::of($period->termMonths());
        $monthsBd   = BigDecimal::of($monthsUsed);

        // 通常法による償却 = 期首簿価 × 償却率 × 月按分
        $regularBd = $bookValueOpening->toBigDecimal()
            ->multipliedBy($rates['rate'])
            ->multipliedBy($monthsBd)
            ->dividedBy($termMonths, 10, BrickRoundingMode::HALF_UP);

        // 償却保証額 = 取得価額 × 保証率 × 月按分
        $guaranteedBd = $rates['assuredRate'] !== null
            ? $acquisition->cost()->toBigDecimal()
                ->multipliedBy($rates['assuredRate'])
                ->multipliedBy($monthsBd)
                ->dividedBy($termMonths, 10, BrickRoundingMode::HALF_UP)
            : null;

        // 通常 < 保証額 → 切替: 改定償却率で期首簿価を均等償却
        $depBd = $regularBd;
        if (
            $rates['switchRate'] !== null
            && $guaranteedBd !== null
            && $regularBd->isLessThan($guaranteedBd)
        ) {
            $depBd = $bookValueOpening->toBigDecimal()
                ->multipliedBy($rates['switchRate'])
                ->multipliedBy($monthsBd)
                ->dividedBy($termMonths, 10, BrickRoundingMode::HALF_UP);
        }

        // 事業供用割合
        if ($acquisition->businessUseRatioPercent() !== 100) {
            $depBd = $depBd
                ->multipliedBy(BigDecimal::of($acquisition->businessUseRatioPercent()))
                ->dividedBy(BigDecimal::of('100'), 10, BrickRoundingMode::HALF_UP);
        }

        $dep = Money::ofYen($depBd->toScale(0, $brickMode));

        // 1円残価で頭打ち
        $maxDep = $bookValueOpening->minus(Money::ofYen(1));
        if ($maxDep->isNegative()) {
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
