<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 一括償却資産 (Lump-sum 3-year).
 *
 * 取得価額10万円以上20万円未満の資産を3年均等で償却する制度.
 *
 * ルール:
 *   - 月按分なし (期中取得でも初年度は cost / 3 全額)
 *   - 1円残価ではなく 0 円まで償却
 *   - 3年で完全に償却 (1年目, 2年目: floor(cost / 3), 3年目: 残額)
 *   - 事業供用割合は通常 100% (法人/個人事業)
 */
final class LumpSumThreeYear
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $roundingMode,
    ): DepreciationResult {
        // 既に完全償却済 → 0
        $remaining = $acquisition->cost()->minus($previousAccumulated);
        if ($remaining->isZero() || $remaining->isNegative()) {
            return new DepreciationResult(
                depreciation: Money::zero(),
                accumulatedClosing: $previousAccumulated,
                bookValueClosing: Money::zero(),
                monthsUsedInPeriod: $period->termMonths(),
            );
        }

        $brickMode = self::brickMode($roundingMode);
        $thirdBd = $acquisition->cost()->toBigDecimal()
            ->dividedBy(BigDecimal::of('3'), 10, BrickRoundingMode::HALF_UP)
            ->toScale(0, $brickMode);
        $third = Money::ofYen($thirdBd);

        // 残額 < 1/3 の場合は残額をすべて (3年目)
        $dep = $third->isGreaterThan($remaining) ? $remaining : $third;

        // 残額が 1/3 以下まで来ていたら、当期で全消化 (最終年度の調整)
        if ($remaining->minus($dep)->isLessThan($third)) {
            // 当期償却後の残が次の1/3未満になるなら、当期で残全部を償却
            $dep = $remaining;
        }

        $accumulated = $previousAccumulated->plus($dep);
        return new DepreciationResult(
            depreciation: $dep,
            accumulatedClosing: $accumulated,
            bookValueClosing: $acquisition->cost()->minus($accumulated),
            monthsUsedInPeriod: $period->termMonths(),
        );
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
