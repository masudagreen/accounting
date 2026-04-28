<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\TrialBalance;

/**
 * 試算表から貸借対照表を構築する.
 *
 * 当期純利益の取扱いについて:
 *   PL 側で算出した当期純利益は、決算締切前の BS では「当期純利益」として右側に独立表示する.
 *   本実装は資産/負債/純資産(資本金等)のみを集計し、当期純利益は ProfitAndLossStatement から
 *   呼出側が取得して BS と組み合わせて表示する.
 */
final class BalanceSheetBuilder
{
    public static function build(
        AccountTree $tree,
        TrialBalance $tb,
        ProfitAndLossStatement $pl,
    ): BalanceSheet {
        $totalAssets = Money::zero();
        $totalLiabilities = Money::zero();
        $totalEquity = Money::zero();

        foreach ($tree->walk() as $node) {
            $title = $node->title();
            $closing = $tb->closingBalance($title->id());
            if ($closing->isZero()) {
                continue;
            }
            switch ($title->classification()) {
                case AccountClassification::Asset:
                    $totalAssets = $totalAssets->plus($closing);
                    break;
                case AccountClassification::Liability:
                    $totalLiabilities = $totalLiabilities->plus($closing);
                    break;
                case AccountClassification::Equity:
                    $totalEquity = $totalEquity->plus($closing);
                    break;
                default:
                    // Revenue/Expense/ManufacturingCost は BS に含めない (PL/CR で集計)
                    break;
            }
        }

        // unused でも引数のドキュメンテーション意図 (締切後のシグネチャに揃える)
        unset($pl);

        return new BalanceSheet(
            totalAssets: $totalAssets,
            totalLiabilities: $totalLiabilities,
            totalEquity: $totalEquity,
        );
    }
}
