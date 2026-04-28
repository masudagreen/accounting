<?php

declare(strict_types=1);

namespace App\Domain\ConsumptionTax;

use Brick\Math\BigDecimal;

/**
 * 消費税率の値オブジェクト。
 *
 * 軽減税率 (8%) と標準税率 (8%, 旧) は数値が同じだが集計上は別概念。
 * 元実装の '8_reduced' フラグに相当する区別を `isReduced` で表現する。
 */
final readonly class TaxRate
{
    private function __construct(
        private BigDecimal $percent,
        private bool $reduced,
        private string $label,
    ) {
    }

    public static function standardTen(): self
    {
        // 2019/10/01〜
        return new self(BigDecimal::of('10'), false, '10');
    }

    public static function reducedEight(): self
    {
        // 2019/10/01〜 軽減税率
        return new self(BigDecimal::of('8'), true, '8_reduced');
    }

    public static function legacyStandardEight(): self
    {
        // 2014/04/01〜2019/09/30 標準税率
        return new self(BigDecimal::of('8'), false, '8');
    }

    public static function legacyFive(): self
    {
        // 〜2014/03/31
        return new self(BigDecimal::of('5'), false, '5');
    }

    public static function zero(): self
    {
        return new self(BigDecimal::zero(), false, '0');
    }

    public function percent(): BigDecimal
    {
        return $this->percent;
    }

    /**
     * 倍率 (例: 10% → 0.10) を BigDecimal で返す。
     */
    public function ratio(): BigDecimal
    {
        return $this->percent->dividedBy(BigDecimal::of('100'), 10, \Brick\Math\RoundingMode::HALF_UP);
    }

    public function isReduced(): bool
    {
        return $this->reduced;
    }

    /**
     * 元実装の `numRateConsumptionTax` 値 ('5'/'8'/'8_reduced'/'10').
     */
    public function label(): string
    {
        return $this->label;
    }
}
