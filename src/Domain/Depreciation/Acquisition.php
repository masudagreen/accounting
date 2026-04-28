<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\Money\Money;

/**
 * 減価償却資産の基本情報.
 *
 * 元実装の `accountingLogFixedAssetsJpn` の主要列に対応:
 *  - numValue          → cost (取得価額)
 *  - numUsefulLife     → usefulLifeYears (耐用年数)
 *  - stampStart        → acquisitionDate (事業供用開始日)
 *  - numRatioOperate   → businessUseRatioPercent (事業供用割合)
 */
final readonly class Acquisition
{
    public function __construct(
        private Money $cost,
        private int $usefulLifeYears,
        private \DateTimeImmutable $acquisitionDate,
        private int $businessUseRatioPercent = 100,
    ) {
        if ($cost->isNegative()) {
            throw new \InvalidArgumentException('cost must be non-negative');
        }
        if ($usefulLifeYears < 1) {
            throw new \InvalidArgumentException('usefulLifeYears must be >= 1');
        }
        if ($businessUseRatioPercent < 0 || $businessUseRatioPercent > 100) {
            throw new \InvalidArgumentException('businessUseRatioPercent must be 0..100');
        }
    }

    public function cost(): Money
    {
        return $this->cost;
    }

    public function usefulLifeYears(): int
    {
        return $this->usefulLifeYears;
    }

    public function acquisitionDate(): \DateTimeImmutable
    {
        return $this->acquisitionDate;
    }

    public function businessUseRatioPercent(): int
    {
        return $this->businessUseRatioPercent;
    }
}
