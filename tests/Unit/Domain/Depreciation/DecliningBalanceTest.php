<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Depreciation;

use App\Domain\Depreciation\Acquisition;
use App\Domain\Depreciation\DecliningBalance;
use App\Domain\Depreciation\DecliningMethod;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 定率法 (Declining-balance) - 平成19年4月1日以降取得分.
 *
 * 計算式:
 *   通常時:    償却額 = 期首簿価 × 償却率
 *   保証額切替: 償却額が「取得価額 × 償却保証率」を下回る年から
 *              改定償却率で残額を均等償却
 *   最終調整:   1円残価で止める
 *
 * 200%定率法 (平成24年4月1日以降取得) と
 * 250%定率法 (平成19年4月1日〜平成24年3月31日取得) は別表.
 */
#[CoversClass(DecliningBalance::class)]
final class DecliningBalanceTest extends TestCase
{
    #[Test]
    public function _200パーセント定率法_耐用5年_初年度(): void
    {
        // 取得価額 1,000,000 / 耐用 5年 / 償却率 0.400 / 改定 0.500 / 保証 0.10800
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            method: DecliningMethod::TwoHundredPercent,
            roundingMode: RoundingMode::Floor,
        );

        // 1,000,000 × 0.400 = 400,000
        self::assertTrue($result->depreciation()->equals(Money::ofYen(400_000)));
    }

    #[Test]
    public function _200パーセント定率法_耐用5年_2年目(): void
    {
        // 期首簿価 = 1,000,000 - 400,000 = 600,000
        // 600,000 × 0.400 = 240,000
        $period = FiscalPeriod::of(2027, 4, 12, 2);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(400_000),
            method: DecliningMethod::TwoHundredPercent,
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue($result->depreciation()->equals(Money::ofYen(240_000)));
    }

    #[Test]
    public function _200パーセント定率法_耐用5年_保証額切替後(): void
    {
        // 累計償却 1,000,000 - 600,000 - 360,000 - 216,000 ... と進む
        // 取得価額 × 保証率 = 1,000,000 × 0.10800 = 108,000
        // 4年目: 期首簿価 = 216,000. 通常計算 = 216,000 × 0.4 = 86,400 < 108,000 → 切替
        // 切替後: 残額 = 216,000. 残期間 = 2年. 改定償却率 0.500 → 216,000 × 0.500 = 108,000
        $period = FiscalPeriod::of(2029, 4, 12, 4);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(784_000), // 1M - 216,000
            method: DecliningMethod::TwoHundredPercent,
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue(
            $result->depreciation()->equals(Money::ofYen(108_000)),
            sprintf('expected 108,000 got %s', $result->depreciation()->toString()),
        );
    }

    #[Test]
    public function _200パーセント定率法_最終年度は1円残価(): void
    {
        // 累計 999,999 → 残 1 → 当期 0
        $period = FiscalPeriod::of(2031, 4, 12, 6);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(999_999),
            method: DecliningMethod::TwoHundredPercent,
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue($result->depreciation()->isZero());
    }

    #[Test]
    public function 期中取得は月按分(): void
    {
        // 取得 2026/8 → 期末まで 8ヶ月
        // 1,000,000 × 0.400 × 8/12 = 266,666.66... → 切捨 266,666
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-08-15'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            method: DecliningMethod::TwoHundredPercent,
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue(
            $result->depreciation()->equals(Money::ofYen(266_666)),
            sprintf('expected 266,666 got %s', $result->depreciation()->toString()),
        );
    }

    #[Test]
    public function _250パーセント定率法_耐用5年_初年度(): void
    {
        // 250% 法 (旧): 耐用5年 → 償却率 0.500
        $period = FiscalPeriod::of(2010, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2010-04-01'),
        );

        $result = DecliningBalance::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            method: DecliningMethod::TwoHundredFiftyPercent,
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue($result->depreciation()->equals(Money::ofYen(500_000)));
    }
}
