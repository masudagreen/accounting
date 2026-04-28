<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Money;

use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 金額の値オブジェクト。
 * 内部表現は BigDecimal (任意精度十進)。float は使わない (誤差を排除するため)。
 * 通貨は JPY 固定で開始 (`accountingEntity.strCurrency` は将来拡張)。
 */
#[CoversClass(Money::class)]
final class MoneyTest extends TestCase
{
    #[Test]
    public function 整数_文字列_BigDecimal_で生成できる(): void
    {
        self::assertTrue(Money::ofYen(100)->equals(Money::ofYen(100)));
        self::assertTrue(Money::ofYen('100')->equals(Money::ofYen(100)));
        self::assertTrue(Money::ofYen(BigDecimal::of('100'))->equals(Money::ofYen(100)));
    }

    #[Test]
    public function ゼロ判定(): void
    {
        self::assertTrue(Money::ofYen(0)->isZero());
        self::assertFalse(Money::ofYen(1)->isZero());
        self::assertFalse(Money::ofYen(-1)->isZero());
    }

    #[Test]
    public function 加算は順序非依存(): void
    {
        $a = Money::ofYen(100);
        $b = Money::ofYen(200);
        self::assertTrue($a->plus($b)->equals($b->plus($a)));
    }

    #[Test]
    public function 減算と符号反転(): void
    {
        $a = Money::ofYen(100);
        self::assertTrue($a->plus($a->negate())->isZero());
        self::assertTrue($a->minus($a)->isZero());
    }

    #[Test]
    public function 乗算_整数倍(): void
    {
        $a = Money::ofYen(100);
        self::assertTrue($a->multipliedBy(3)->equals(Money::ofYen(300)));
    }

    #[Test]
    public function 大小比較(): void
    {
        self::assertTrue(Money::ofYen(100)->isLessThan(Money::ofYen(200)));
        self::assertTrue(Money::ofYen(200)->isGreaterThan(Money::ofYen(100)));
        self::assertTrue(Money::ofYen(100)->isLessThanOrEqualTo(Money::ofYen(100)));
        self::assertTrue(Money::ofYen(100)->isGreaterThanOrEqualTo(Money::ofYen(100)));
    }

    #[Test]
    public function 端数処理_整数化(): void
    {
        $m = Money::ofYen('100.4');
        self::assertTrue($m->roundedToYen(RoundingMode::Floor)->equals(Money::ofYen(100)));
        self::assertTrue($m->roundedToYen(RoundingMode::Ceil)->equals(Money::ofYen(101)));
        self::assertTrue($m->roundedToYen(RoundingMode::Round)->equals(Money::ofYen(100)));

        $m2 = Money::ofYen('100.5');
        self::assertTrue($m2->roundedToYen(RoundingMode::Round)->equals(Money::ofYen(101)));
    }

    #[Test]
    public function 不変性_演算は新インスタンスを返す(): void
    {
        $a = Money::ofYen(100);
        $b = $a->plus(Money::ofYen(50));
        self::assertTrue($a->equals(Money::ofYen(100))); // a は不変
        self::assertTrue($b->equals(Money::ofYen(150)));
    }

    #[Test]
    public function 浮動小数誤差を出さない(): void
    {
        // 0.1 + 0.2 == 0.3 が float では成立しないが、Money では成立する
        $a = Money::ofYen('0.1');
        $b = Money::ofYen('0.2');
        $c = Money::ofYen('0.3');
        self::assertTrue($a->plus($b)->equals($c));
    }
}
