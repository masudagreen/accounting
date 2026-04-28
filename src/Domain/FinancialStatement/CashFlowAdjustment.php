<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * キャッシュフロー計算書における個別の調整項目.
 *
 * 間接法では PL の純利益から出発して各項目を加減算するが、
 * 投資・財務活動の項目は試算表から自動導出できないため外部入力とする.
 *
 * amount の符号規則:
 *   正 = 現金の増加方向
 *   負 = 現金の減少方向
 */
final readonly class CashFlowAdjustment
{
    private function __construct(
        private CashFlowSection $section,
        private Money $amount,
        private string $description,
    ) {
    }

    public static function of(
        CashFlowSection $section,
        Money $amount,
        string $description = '',
    ): self {
        return new self($section, $amount, $description);
    }

    public function section(): CashFlowSection
    {
        return $this->section;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function description(): string
    {
        return $this->description;
    }
}
