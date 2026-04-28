<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 貸借対照表 (Balance Sheet).
 *
 * 期末締切前の試算表に対する不変条件:
 *   totalAssets == totalLiabilities + totalEquity + 当期純利益
 *
 * 締切後 (利益剰余金への振替後) は:
 *   totalAssets == totalLiabilities + totalEquity (利益剰余金に既に net income が含まれる)
 */
final readonly class BalanceSheet
{
    public function __construct(
        private Money $totalAssets,
        private Money $totalLiabilities,
        private Money $totalEquity,
    ) {
    }

    public function totalAssets(): Money
    {
        return $this->totalAssets;
    }

    public function totalLiabilities(): Money
    {
        return $this->totalLiabilities;
    }

    public function totalEquity(): Money
    {
        return $this->totalEquity;
    }
}
