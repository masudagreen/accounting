<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * キャッシュフロー計算書 (間接法).
 *
 * 不変条件:
 *   netCashChange() = operatingCashFlow() + investingCashFlow() + financingCashFlow()
 *   closingCash()   = openingCash() + netCashChange()
 */
final readonly class CashFlowStatement
{
    public function __construct(
        /** 税引前当期純利益 */
        private Money $incomeBeforeTax,
        /** 減価償却費 (非現金費用として加算) */
        private Money $depreciation,
        /** 売上債権の増減 (増加は現金減少 = 負) */
        private Money $changeInAccountsReceivable,
        /** 棚卸資産の増減 (増加は現金減少 = 負) */
        private Money $changeInInventory,
        /** 仕入債務の増減 (増加は現金増加 = 正) */
        private Money $changeInAccountsPayable,
        /** 投資活動の調整合計 */
        private Money $investingCashFlow,
        /** 財務活動の調整合計 */
        private Money $financingCashFlow,
        /** 期首現金等 */
        private Money $openingCash,
        /** 期末現金等 */
        private Money $closingCash,
    ) {
    }

    /** 税引前当期純利益 */
    public function incomeBeforeTax(): Money
    {
        return $this->incomeBeforeTax;
    }

    /** 減価償却費 */
    public function depreciation(): Money
    {
        return $this->depreciation;
    }

    /** 売上債権増減額 (増加 = 負値) */
    public function changeInAccountsReceivable(): Money
    {
        return $this->changeInAccountsReceivable;
    }

    /** 棚卸資産増減額 (増加 = 負値) */
    public function changeInInventory(): Money
    {
        return $this->changeInInventory;
    }

    /** 仕入債務増減額 (増加 = 正値) */
    public function changeInAccountsPayable(): Money
    {
        return $this->changeInAccountsPayable;
    }

    /**
     * 営業活動によるキャッシュフロー (間接法).
     *
     * = 税引前純利益
     *   + 減価償却費
     *   - 売上債権の増加 (増加はマイナス)
     *   - 棚卸資産の増加 (増加はマイナス)
     *   + 仕入債務の増加 (増加はプラス)
     *
     * changeInAccountsReceivable / changeInInventory / changeInAccountsPayable は
     * すでに符号調整済みで保持しているため、単純加算する.
     */
    public function operatingCashFlow(): Money
    {
        return $this->incomeBeforeTax
            ->plus($this->depreciation)
            ->plus($this->changeInAccountsReceivable)
            ->plus($this->changeInInventory)
            ->plus($this->changeInAccountsPayable);
    }

    /** 投資活動によるキャッシュフロー */
    public function investingCashFlow(): Money
    {
        return $this->investingCashFlow;
    }

    /** 財務活動によるキャッシュフロー */
    public function financingCashFlow(): Money
    {
        return $this->financingCashFlow;
    }

    /** 現金等の増減額 = 営業CF + 投資CF + 財務CF */
    public function netCashChange(): Money
    {
        return $this->operatingCashFlow()
            ->plus($this->investingCashFlow)
            ->plus($this->financingCashFlow);
    }

    /** 期首現金等 */
    public function openingCash(): Money
    {
        return $this->openingCash;
    }

    /** 期末現金等 = 期首 + 増減 */
    public function closingCash(): Money
    {
        return $this->closingCash;
    }
}
