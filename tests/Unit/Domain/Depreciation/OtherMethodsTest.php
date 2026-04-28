<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Depreciation;

use App\Domain\Depreciation\Acquisition;
use App\Domain\Depreciation\Average;
use App\Domain\Depreciation\LumpSumThreeYear;
use App\Domain\Depreciation\SumOfYears;
use App\Domain\Depreciation\Voluntary;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 残りの減価償却法.
 *
 * - Voluntary (任意償却): 利用者が金額を指定する. 1円残でクランプのみ.
 * - Average (平均償却):   取得価額 / (耐用年数 × 12) 月別均等.
 * - SumOfYears (級数法):  年度n → (耐用年数 - n + 1) / Σ k=1..N k.
 * - LumpSumThreeYear (一括償却資産): 取得価額 / 3 を3年均等. 月按分なし.
 */
#[CoversClass(Voluntary::class)]
#[CoversClass(Average::class)]
#[CoversClass(SumOfYears::class)]
#[CoversClass(LumpSumThreeYear::class)]
final class OtherMethodsTest extends TestCase
{
    // ===== Voluntary (任意償却) =====

    #[Test]
    public function 任意償却_利用者指定額をそのまま使う(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_000_000),
            usefulLifeYears: 10,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
        $result = Voluntary::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            requestedAmount: Money::ofYen(150_000),
        );
        self::assertTrue($result->depreciation()->equals(Money::ofYen(150_000)));
    }

    #[Test]
    public function 任意償却_1円残でクランプ(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(100_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2020-04-01'),
        );
        // 累計99,999. 残1円. 利用者が10000指定しても上限は0
        $result = Voluntary::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(99_999),
            requestedAmount: Money::ofYen(10_000),
        );
        self::assertTrue($result->depreciation()->isZero());
    }

    // ===== Average (平均償却) =====

    #[Test]
    public function 平均償却_耐用5年_満期_期首取得(): void
    {
        // 1,200,000 / (5 * 12) = 20,000 /月. 12ヶ月使用 → 240,000
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
        $result = Average::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($result->depreciation()->equals(Money::ofYen(240_000)));
    }

    #[Test]
    public function 平均償却_期中8月取得(): void
    {
        // 期首から8月まで4ヶ月過ぎ. 8月から3月まで残り8ヶ月.
        // 1,200,000 / 60ヶ月 = 20,000 × 8 = 160,000
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_200_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-08-15'),
        );
        $result = Average::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($result->depreciation()->equals(Money::ofYen(160_000)));
    }

    // ===== SumOfYears (級数法) =====

    #[Test]
    public function 級数法_耐用5年_初年度(): void
    {
        // 級数和 = 1+2+3+4+5 = 15
        // 1年目 = 取得 × 5/15 = 取得 × 1/3
        // 1,500,000 × 5/15 = 500,000
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_500_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
        $result = SumOfYears::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            yearIndex: 1, // 1始まり
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue(
            $result->depreciation()->equals(Money::ofYen(500_000)),
            sprintf('expected 500,000 got %s', $result->depreciation()->toString()),
        );
    }

    #[Test]
    public function 級数法_耐用5年_2年目(): void
    {
        // 2年目 = 1,500,000 × 4/15 = 400,000
        $period = FiscalPeriod::of(2027, 4, 12, 2);
        $acquisition = new Acquisition(
            cost: Money::ofYen(1_500_000),
            usefulLifeYears: 5,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
        $result = SumOfYears::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(500_000),
            yearIndex: 2,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($result->depreciation()->equals(Money::ofYen(400_000)));
    }

    // ===== LumpSumThreeYear (一括償却資産) =====

    #[Test]
    public function 一括償却資産_3年均等_初年度(): void
    {
        // 取得 150,000 → 50,000 / 年 (月按分なし)
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        $acquisition = new Acquisition(
            cost: Money::ofYen(150_000),
            usefulLifeYears: 3, // 一括は耐用年数3年扱い
            acquisitionDate: new \DateTimeImmutable('2026-08-15'), // 期中取得でも按分なし
        );
        $result = LumpSumThreeYear::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::zero(),
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($result->depreciation()->equals(Money::ofYen(50_000)));
    }

    #[Test]
    public function 一括償却資産_3年均等_最終年度(): void
    {
        // 取得 100,000 → 33,333 + 33,333 + 33,334
        // 3年目: cost - prevAccumulated = 33,334
        $period = FiscalPeriod::of(2028, 4, 12, 3);
        $acquisition = new Acquisition(
            cost: Money::ofYen(100_000),
            usefulLifeYears: 3,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
        $result = LumpSumThreeYear::compute(
            acquisition: $acquisition,
            period: $period,
            previousAccumulated: Money::ofYen(66_666),
            roundingMode: RoundingMode::Floor,
        );
        // 3年目は残額をすべて償却 (1円残価ではなく0円まで)
        self::assertTrue(
            $result->depreciation()->equals(Money::ofYen(33_334)),
            sprintf('expected 33,334 got %s', $result->depreciation()->toString()),
        );
    }
}
