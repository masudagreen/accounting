<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Domain\FixedAsset\Rate\DecliningBalanceRateTable;
use Rucaro\Support\Decimal\Decimal;

/**
 * 定率法 (declining-balance method).
 *
 * Dispatches against the right rate table based on `$method`:
 *   - `declining_balance_2007` : 250% declining rates
 *   - `declining_balance_2012` : 200% declining rates (post-2012 reform)
 *   - `declining_balance_2016` : 200% for newly acquired machinery
 *   - `declining_balance`      : alias that resolves to the 200% table
 *
 * Core rule (保証率):
 *   1. `computedDep = opening_book_value * rate` (pro-rated by months)
 *   2. If `computedDep < acquisition_cost * assuredRate`, switch to the
 *      "改定償却率" (updateRate) and apply it straight-line on the opening
 *      book value so the remaining life amortizes evenly.
 *
 * Final 1 yen memo retention handled identically to straight line.
 */
final class DecliningBalanceDepreciationCalculator implements DepreciationCalculatorInterface
{
    public function __construct(
        /** @var 'declining_balance'|'declining_balance_2007'|'declining_balance_2012'|'declining_balance_2016' */
        private readonly string $method = 'declining_balance_2012',
    ) {
    }

    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult
    {
        $row = DecliningBalanceRateTable::lookup($this->method, $request->usefulLifeYears);
        if ($row === null) {
            // Fall back to straight line for unusual useful lives.
            return (new StraightLineDepreciationCalculator())->calculate($request);
        }

        $rate = $row['rate'];
        $updateRate = $row['updateRate'];
        $assuredRate = $row['assuredRate'];

        // 保証額 = 取得価額 * 保証率（切り捨て）
        $assuredAmount = DecimalMath::mulFloor($request->acquisitionCost, $assuredRate);

        // 当期計算分（年額）= 期首簿価 × 償却率
        $computedAnnual = DecimalMath::mulFloor($request->openingBookValue, $rate);

        // 保証額を下回るなら改定償却率（改定後は straight-line）
        $useUpdate = Decimal::compare($computedAnnual, $assuredAmount) < 0;
        if ($useUpdate && $updateRate > 0) {
            $computedAnnual = DecimalMath::mulFloor($request->openingBookValue, $updateRate);
        }

        // 月割（期中取得など）
        $months = $request->monthsInService;
        $totalMonths = $request->fiscalTermMonths > 0 ? $request->fiscalTermMonths : 12;
        $dep = DecimalMath::mulFloor($computedAnnual, $months / $totalMonths);

        // 簿価が 1 円（備忘）を割り込まないように
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
