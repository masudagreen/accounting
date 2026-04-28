<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Depreciation;

use App\Domain\Depreciation\Acquisition;
use App\Domain\Depreciation\StraightLine;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 定額法 (Straight-line method) - 平成19年4月1日以降取得分.
 *
 * 計算式:
 *   年間償却費 = 取得価額 × 償却率
 *   月按分     = 年間償却費 × (期内使用月数 / 12)
 *   最終年度  = (期首簿価 - 1円) で止める (1円残価)
 *
 * 償却率は耐用年数から表引き (depStraightNew.csv).
 *  例: 耐用年数 5年 → 償却率 0.200
 *      耐用年数 10年 → 償却率 0.100
 */
#[CoversClass(StraightLine::class)]
final class StraightLineTest extends TestCase
{
    #[Test]
    public function 期首取得_満1年使用_耐用年数5年(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );

        // 1,200,000 × 0.200 = 240,000
        self::assertTrue($result->depreciation()->equals(Money::ofYen(240_000)));
    }

    #[Test]
    public function 期中8月取得_期末まで8ヶ月使用(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-08-15'),
        );

        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );

        // 1,200,000 × 0.200 × 8/12 = 160,000
        self::assertTrue($result->depreciation()->equals(Money::ofYen(160_000)));
    }

    #[Test]
    public function 最終年度は1円残価で止まる(): void
    {
        // 取得価額 1,000,000 / 耐用年数 5年 / 償却率 0.200 → 年間 200,000
        // 4年目までで 800,000 償却済
        // 5年目: 1,000,000 - 1 - 800,000 = 199,999 (1円残)
        $period = FiscalPeriod::of(2030, 4, 12, 5);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(800_000),
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue(
            $result->depreciation()->equals(Money::ofYen(199_999)),
            sprintf('expected 199,999 got %s', $result->depreciation()->toString()),
        );
    }

    #[Test]
    public function 償却済の資産は0円(): void
    {
        // 既に簿価1円まで償却済 (残価) → 当期償却0円
        $period = FiscalPeriod::of(2031, 4, 12, 6);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );

        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(999_999),
            roundingMode: RoundingMode::Floor,
        );

        self::assertTrue($result->depreciation()->isZero());
    }

    #[Test]
    public function 事業供用割合90パーセント(): void
    {
        // 個人事業主の自宅兼事務所等で 90% 業務使用
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            businessUseRatioPercent: 90,
        );

        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );

        // 1,200,000 × 0.200 × 0.90 = 216,000
        self::assertTrue($result->depreciation()->equals(Money::ofYen(216_000)));
    }

    #[Test]
    public function 取得日が期外なら例外(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2025-04-01'), // 当期外でも、過去の継続資産として扱える必要あり
        );

        // 過去取得の資産は当期で全額償却。期外取得日は許容する
        $result = StraightLine::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(240_000), // 前期に1年償却済
            roundingMode: RoundingMode::Floor,
        );

        // 当期は 12 ヶ月 × 0.200 = 240,000
        self::assertTrue($result->depreciation()->equals(Money::ofYen(240_000)));
    }
}
