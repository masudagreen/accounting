<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 勘定科目の大区分。
 *
 *  - Asset             資産     (BS, 借方)
 *  - Liability         負債     (BS, 貸方)
 *  - Equity            純資産   (BS, 貸方)
 *  - Revenue           収益     (PL, 貸方)
 *  - Expense           費用     (PL, 借方)
 *  - ManufacturingCost 製造原価 (CR, 借方)
 */
enum AccountClassification: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';
    case ManufacturingCost = 'manufacturingCost';

    public function normalBalance(): NormalBalance
    {
        return match ($this) {
            self::Asset, self::Expense, self::ManufacturingCost => NormalBalance::Debit,
            self::Liability, self::Equity, self::Revenue => NormalBalance::Credit,
        };
    }

    public function isBalanceSheet(): bool
    {
        return match ($this) {
            self::Asset, self::Liability, self::Equity => true,
            default => false,
        };
    }

    public function isProfitAndLoss(): bool
    {
        return match ($this) {
            self::Revenue, self::Expense => true,
            default => false,
        };
    }

    public function isCostReport(): bool
    {
        return $this === self::ManufacturingCost;
    }
}
