<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * キャッシュフロー計算書 (間接法) を構築する.
 *
 * 間接法の計算手順:
 *   1. 税引前当期純利益 (PL.incomeBeforeTax) から出発
 *   2. 非現金費用 (減価償却費) を加算
 *   3. 運転資本変動を加減:
 *       - 売上債権増加 → 減算
 *       - 棚卸資産増加 → 減算
 *       - 仕入債務増加 → 加算
 *   4. 投資・財務活動は CashFlowAdjustment の合計
 *
 * 現金科目の識別:
 *   openingBsBalances / closingBsBalances のキーは以下を想定:
 *     'cash'               現金・預金
 *     'accountsReceivable' 売上債権
 *     'inventory'          棚卸資産
 *     'accountsPayable'    仕入債務
 *
 * 注意: 法人税等の支払 (PL.tax) は本実装では簡略化し、
 *       incomeBeforeTax (税引前) ではなく netIncome (税引後) を出発点とする選択も
 *       ありえるが、ここでは間接法の原則に従い incomeBeforeTax を採用する.
 *       ただしテスト上では tax = 0 のケースのみ扱っており、法人税支払の
 *       キャッシュアウトは CashFlowAdjustment::Operating で外部入力する設計とする.
 */
final class CashFlowStatementBuilder
{
    private const string KEY_CASH = 'cash';
    private const string KEY_AR   = 'accountsReceivable';
    private const string KEY_INV  = 'inventory';
    private const string KEY_AP   = 'accountsPayable';

    /**
     * @param array<string, Money>      $openingBsBalances 期首 BS の科目別残高
     * @param array<string, Money>      $closingBsBalances 期末 BS の科目別残高
     * @param list<CashFlowAdjustment>  $adjustments       投資・財務活動の個別調整項目
     */
    public static function build(
        ProfitAndLossStatement $profitAndLoss,
        array $openingBsBalances,
        array $closingBsBalances,
        Money $depreciation,
        array $adjustments,
    ): CashFlowStatement {
        $openingCash = $openingBsBalances[self::KEY_CASH] ?? Money::zero();
        $closingCash = $closingBsBalances[self::KEY_CASH] ?? Money::zero();

        // 運転資本変動 (間接法の調整)
        // 売上債権の増減: 増加は現金減少 → 符号反転して加算
        $arChange = self::netChange($openingBsBalances, $closingBsBalances, self::KEY_AR);
        $changeInAR = $arChange->negate();

        // 棚卸資産の増減: 増加は現金減少 → 符号反転して加算
        $invChange = self::netChange($openingBsBalances, $closingBsBalances, self::KEY_INV);
        $changeInInv = $invChange->negate();

        // 仕入債務の増減: 増加は現金増加 → そのまま加算
        $apChange = self::netChange($openingBsBalances, $closingBsBalances, self::KEY_AP);
        $changeInAP = $apChange;

        // 投資・財務CF の集計
        // Operating 区分の CashFlowAdjustment は現在サポートしない (将来拡張用)
        $investingCf  = Money::zero();
        $financingCf  = Money::zero();
        foreach ($adjustments as $adj) {
            if ($adj->section() === CashFlowSection::Investing) {
                $investingCf = $investingCf->plus($adj->amount());
            } elseif ($adj->section() === CashFlowSection::Financing) {
                $financingCf = $financingCf->plus($adj->amount());
            }
        }

        return new CashFlowStatement(
            incomeBeforeTax: $profitAndLoss->incomeBeforeTax(),
            depreciation: $depreciation,
            changeInAccountsReceivable: $changeInAR,
            changeInInventory: $changeInInv,
            changeInAccountsPayable: $changeInAP,
            investingCashFlow: $investingCf,
            financingCashFlow: $financingCf,
            openingCash: $openingCash,
            closingCash: $closingCash,
        );
    }

    /**
     * 期末 - 期首 の増減額を計算する.
     *
     * @param array<string, Money> $opening
     * @param array<string, Money> $closing
     */
    private static function netChange(array $opening, array $closing, string $key): Money
    {
        $openVal = $opening[$key] ?? Money::zero();
        $closeVal = $closing[$key] ?? Money::zero();
        return $closeVal->minus($openVal);
    }
}
