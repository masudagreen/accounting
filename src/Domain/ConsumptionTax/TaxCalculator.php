<?php

declare(strict_types=1);

namespace App\Domain\ConsumptionTax;

use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 消費税額計算 (純関数).
 *
 * 元実装の対応:
 *  - 外税 (Exclusive): tax = round(net * rate, mode)
 *  - 内税 (Inclusive): netExcl = round(gross * 100 / (100 + rate), mode); tax = gross - netExcl
 *  - 別記 (Separate): 計算対象外 (ユーザ入力値を信頼)
 */
final class TaxCalculator
{
    public static function computeTax(
        Money $net,
        TaxRate $rate,
        TaxTreatment $treatment,
        RoundingMode $roundingMode,
    ): Money {
        if ($treatment === TaxTreatment::Separate) {
            throw new \InvalidArgumentException(
                'TaxTreatment::Separate uses user-supplied tax; do not call computeTax.',
            );
        }

        if ($net->isZero() || $rate->percent()->isZero()) {
            return Money::zero();
        }

        $brickMode = self::brickMode($roundingMode);

        return $treatment === TaxTreatment::Exclusive
            ? self::taxOnExclusive($net, $rate, $brickMode)
            : self::taxOnInclusive($net, $rate, $brickMode);
    }

    private static function taxOnExclusive(Money $net, TaxRate $rate, BrickRoundingMode $mode): Money
    {
        // tax = net * rate%
        $taxBd = $net->toBigDecimal()
            ->multipliedBy($rate->percent())
            ->dividedBy(BigDecimal::of('100'), 10, BrickRoundingMode::HALF_UP)
            ->toScale(0, $mode);

        return Money::ofYen($taxBd);
    }

    private static function taxOnInclusive(Money $gross, TaxRate $rate, BrickRoundingMode $mode): Money
    {
        // netExcl = gross * 100 / (100 + rate)
        $denominator = BigDecimal::of('100')->plus($rate->percent());
        $netExcl = $gross->toBigDecimal()
            ->multipliedBy(BigDecimal::of('100'))
            ->dividedBy($denominator, 10, BrickRoundingMode::HALF_UP)
            ->toScale(0, $mode);

        $tax = $gross->toBigDecimal()->minus($netExcl);
        return Money::ofYen($tax);
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
