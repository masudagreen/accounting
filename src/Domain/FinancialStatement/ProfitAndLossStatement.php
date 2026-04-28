<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 損益計算書 (Profit and Loss Statement).
 *
 * 各セクションの合計を保持し、利益指標を導出する.
 */
final readonly class ProfitAndLossStatement
{
    public function __construct(
        private Money $sales,
        private Money $costOfSales,
        private Money $sellingAndAdmin,
        private Money $nonOperatingIncome,
        private Money $nonOperatingExpenses,
        private Money $extraordinaryIncome,
        private Money $extraordinaryLosses,
        private Money $tax,
    ) {
    }

    public function sales(): Money
    {
        return $this->sales;
    }

    public function costOfSales(): Money
    {
        return $this->costOfSales;
    }

    public function sellingAndAdmin(): Money
    {
        return $this->sellingAndAdmin;
    }

    public function nonOperatingIncome(): Money
    {
        return $this->nonOperatingIncome;
    }

    public function nonOperatingExpenses(): Money
    {
        return $this->nonOperatingExpenses;
    }

    public function extraordinaryIncome(): Money
    {
        return $this->extraordinaryIncome;
    }

    public function extraordinaryLosses(): Money
    {
        return $this->extraordinaryLosses;
    }

    public function tax(): Money
    {
        return $this->tax;
    }

    /** 売上総利益 = 売上 - 売上原価. */
    public function grossProfit(): Money
    {
        return $this->sales->minus($this->costOfSales);
    }

    /** 営業利益 = 売上総利益 - 販管費. */
    public function operatingIncome(): Money
    {
        return $this->grossProfit()->minus($this->sellingAndAdmin);
    }

    /** 経常利益 = 営業利益 + 営業外収益 - 営業外費用. */
    public function ordinaryIncome(): Money
    {
        return $this->operatingIncome()
            ->plus($this->nonOperatingIncome)
            ->minus($this->nonOperatingExpenses);
    }

    /** 税引前当期純利益 = 経常利益 + 特別利益 - 特別損失. */
    public function incomeBeforeTax(): Money
    {
        return $this->ordinaryIncome()
            ->plus($this->extraordinaryIncome)
            ->minus($this->extraordinaryLosses);
    }

    /** 当期純利益 = 税引前当期純利益 - 法人税等. */
    public function netIncome(): Money
    {
        return $this->incomeBeforeTax()->minus($this->tax);
    }
}
