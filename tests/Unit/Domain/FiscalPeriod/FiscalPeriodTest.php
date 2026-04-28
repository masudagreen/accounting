<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FiscalPeriod;

use App\Domain\FiscalPeriod\FiscalPeriod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 会計期間 (Fiscal Period) の値オブジェクト。
 *
 * 不変条件:
 *  - 期首月: 1〜12
 *  - 期間月数: 1〜12 (通常12, 不規則決算で異なる)
 *  - 期番号: 1以上 (numFiscalPeriod / numFiscalBeginningYear から導出)
 *
 * 計算対象:
 *  - 期首月日 / 期末月日 (年跨ぎを考慮)
 *  - 期内の任意日付の所属判定
 *  - 月数の経過判定 (期中取得時の按分用)
 *  - 半期/四半期/月別の集計境界
 */
#[CoversClass(FiscalPeriod::class)]
final class FiscalPeriodTest extends TestCase
{
    #[Test]
    public function 通常_4月始まり_12ヶ月決算(): void
    {
        $period = FiscalPeriod::of(
            beginningYear: 2026,
            beginningMonth: 4,
            termMonths: 12,
            number: 5,
        );

        self::assertSame(2026, $period->startDate()->format('Y') * 1);
        self::assertSame(4, (int) $period->startDate()->format('n'));
        self::assertSame(1, (int) $period->startDate()->format('j'));

        self::assertSame(2027, (int) $period->endDate()->format('Y'));
        self::assertSame(3, (int) $period->endDate()->format('n'));
        self::assertSame(31, (int) $period->endDate()->format('j'));

        self::assertSame(5, $period->number());
    }

    #[Test]
    public function 暦年_1月始まり(): void
    {
        $period = FiscalPeriod::of(2026, 1, 12, 1);
        self::assertSame('2026-01-01', $period->startDate()->format('Y-m-d'));
        self::assertSame('2026-12-31', $period->endDate()->format('Y-m-d'));
    }

    #[Test]
    public function 不規則決算_6ヶ月_短期事業年度(): void
    {
        // 設立第1期や決算期変更で発生する短期事業年度
        $period = FiscalPeriod::of(2026, 4, 6, 1);
        self::assertSame('2026-04-01', $period->startDate()->format('Y-m-d'));
        self::assertSame('2026-09-30', $period->endDate()->format('Y-m-d'));
    }

    #[Test]
    public function 期内の日付判定(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 5);

        self::assertTrue($period->contains(new \DateTimeImmutable('2026-04-01')));
        self::assertTrue($period->contains(new \DateTimeImmutable('2026-12-31')));
        self::assertTrue($period->contains(new \DateTimeImmutable('2027-03-31')));
        self::assertFalse($period->contains(new \DateTimeImmutable('2026-03-31')));
        self::assertFalse($period->contains(new \DateTimeImmutable('2027-04-01')));
    }

    #[Test]
    public function 期首月の境界値(): void
    {
        FiscalPeriod::of(2026, 1, 12, 1);  // OK
        FiscalPeriod::of(2026, 12, 12, 1); // OK

        $this->expectException(\InvalidArgumentException::class);
        FiscalPeriod::of(2026, 0, 12, 1);
    }

    #[Test]
    public function 期首月_13は不正(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FiscalPeriod::of(2026, 13, 12, 1);
    }

    #[Test]
    public function 月数_0は不正(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FiscalPeriod::of(2026, 4, 0, 1);
    }

    #[Test]
    public function 月数_13は不正(): void
    {
        // 元実装は12までを想定
        $this->expectException(\InvalidArgumentException::class);
        FiscalPeriod::of(2026, 4, 13, 1);
    }

    #[Test]
    public function 期番号は1以上(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FiscalPeriod::of(2026, 4, 12, 0);
    }

    #[Test]
    public function 翌期_通常(): void
    {
        $current = FiscalPeriod::of(2026, 4, 12, 5);
        $next = $current->next();
        self::assertSame('2027-04-01', $next->startDate()->format('Y-m-d'));
        self::assertSame('2028-03-31', $next->endDate()->format('Y-m-d'));
        self::assertSame(6, $next->number());
    }

    #[Test]
    public function 期中の月数経過_期首月から3ヶ月目まで(): void
    {
        $period = FiscalPeriod::of(2026, 4, 12, 1);
        // 取得日 4月 → 1ヶ月使用 (期首月)
        self::assertSame(12, $period->monthsRemaining(new \DateTimeImmutable('2026-04-15')));
        // 取得日 6月 → 残り 10ヶ月
        self::assertSame(10, $period->monthsRemaining(new \DateTimeImmutable('2026-06-15')));
        // 期末月 → 残り 1ヶ月
        self::assertSame(1, $period->monthsRemaining(new \DateTimeImmutable('2027-03-15')));
    }
}
