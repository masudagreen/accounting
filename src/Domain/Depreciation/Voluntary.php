<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;

/**
 * 任意償却 (Voluntary).
 *
 * 利用者が指定した金額をそのまま当期償却額とする.
 * 残簿価が1円未満になる金額は受け付けず、残簿価1円までクランプする.
 */
final class Voluntary
{
    public static function compute(
        Acquisition $acquisition,
        FiscalPeriod $period,
        Money $previousAccumulated,
        Money $requestedAmount,
    ): DepreciationResult {
        if ($requestedAmount->isNegative()) {
            throw new \InvalidArgumentException('requestedAmount must be non-negative');
        }

        $bookValueOpening = $acquisition->cost()->minus($previousAccumulated);
        $maxDep = $bookValueOpening->minus(Money::ofYen(1));

        $dep = $requestedAmount;
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
            monthsUsedInPeriod: $period->termMonths(),
        );
    }
}
