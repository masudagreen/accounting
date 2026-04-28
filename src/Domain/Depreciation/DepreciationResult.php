<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use App\Domain\Money\Money;

/**
 * 1期分の減価償却計算結果.
 */
final readonly class DepreciationResult
{
    public function __construct(
        private Money $depreciation,
        private Money $accumulatedClosing,
        private Money $bookValueClosing,
        private int $monthsUsedInPeriod,
    ) {
    }

    /** 当期償却費 (事業供用割合反映後). */
    public function depreciation(): Money
    {
        return $this->depreciation;
    }

    /** 期末減価償却累計額. */
    public function accumulatedClosing(): Money
    {
        return $this->accumulatedClosing;
    }

    /** 期末簿価. */
    public function bookValueClosing(): Money
    {
        return $this->bookValueClosing;
    }

    /** 当期内の使用月数 (期中取得時の按分基礎). */
    public function monthsUsedInPeriod(): int
    {
        return $this->monthsUsedInPeriod;
    }
}
