<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ConsumptionTax;

use App\Domain\ConsumptionTax\TaxCalculator;
use App\Domain\ConsumptionTax\TaxRate;
use App\Domain\ConsumptionTax\TaxTreatment;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 消費税計算の最初の不変条件: 端数処理ポリシーが反映されること。
 *
 * 計算式 (税抜入力 = 外税):
 *   tax = round(net * rate, 0, mode)
 *
 * 計算式 (税込入力 = 内税):
 *   net = round(gross * 100 / (100 + rate), 0, mode)  ※元実装は税抜額を先に求める
 *   tax = gross - net
 *
 * mode は accountingEntityJpn.flagConsumptionTaxCalc:
 *   1=切捨, 2=四捨五入, 3=切上
 */
#[CoversClass(TaxCalculator::class)]
final class TaxCalculatorTest extends TestCase
{
    #[Test]
    public function 外税_10パーセント_切捨(): void
    {
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1000),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(100)));
    }

    #[Test]
    public function 外税_8パーセント軽減_切捨(): void
    {
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1234),
            rate: TaxRate::reducedEight(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Floor,
        );
        // 1234 * 0.08 = 98.72 → 切捨 → 98
        self::assertTrue($tax->equals(Money::ofYen(98)));
    }

    #[Test]
    public function 外税_10パーセント_四捨五入(): void
    {
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1235),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Round,
        );
        // 1235 * 0.10 = 123.5 → 四捨五入 → 124
        self::assertTrue($tax->equals(Money::ofYen(124)));
    }

    #[Test]
    public function 外税_10パーセント_切上(): void
    {
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1001),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Ceil,
        );
        // 1001 * 0.10 = 100.1 → 切上 → 101
        self::assertTrue($tax->equals(Money::ofYen(101)));
    }

    #[Test]
    public function 内税_10パーセント_切捨(): void
    {
        // 税込1100円 で内税10% → 税抜1000円, 税100円
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1100),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Inclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(100)));
    }

    #[Test]
    public function 内税_8パーセント軽減_切捨(): void
    {
        // 税込1080円, 軽減税率8% → 税抜1000円, 税80円
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1080),
            rate: TaxRate::reducedEight(),
            treatment: TaxTreatment::Inclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(80)));
    }

    #[Test]
    public function 内税_端数があるケース(): void
    {
        // 税込1234円, 10% → 税抜 1234 * 100 / 110 = 1121.818... → 切捨 1121, 税 113
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1234),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Inclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(113)));
    }

    #[Test]
    public function 別記_税額は外部入力_計算しない(): void
    {
        // 別記の場合、税額はユーザ入力値をそのまま使うため computeTax は対象外
        // 仕様として「TaxTreatment::Separate では呼ばないこと」を例外で明示する
        $this->expectException(\InvalidArgumentException::class);
        TaxCalculator::computeTax(
            net: Money::ofYen(1000),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Separate,
            roundingMode: RoundingMode::Floor,
        );
    }

    #[Test]
    public function ゼロ円なら税もゼロ(): void
    {
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(0),
            rate: TaxRate::standardTen(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->isZero());
    }

    #[Test]
    public function 旧税率_5パーセント(): void
    {
        // 旧税率5%もサポート (経過措置取引や過去年度の集計用)
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1000),
            rate: TaxRate::legacyFive(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(50)));
    }

    #[Test]
    public function 旧税率_8パーセント_標準(): void
    {
        // 2014/4-2019/9 の標準税率8% (軽減ではない)
        $tax = TaxCalculator::computeTax(
            net: Money::ofYen(1000),
            rate: TaxRate::legacyStandardEight(),
            treatment: TaxTreatment::Exclusive,
            roundingMode: RoundingMode::Floor,
        );
        self::assertTrue($tax->equals(Money::ofYen(80)));
    }

    #[Test]
    public function 軽減税率8と標準8は別概念(): void
    {
        // どちらも数値は8%だが、集計上は別管理 (元実装の '8_reduced' フラグ相当)
        self::assertNotSame(TaxRate::reducedEight(), TaxRate::legacyStandardEight());
        self::assertTrue(TaxRate::reducedEight()->isReduced());
        self::assertFalse(TaxRate::legacyStandardEight()->isReduced());
    }
}
