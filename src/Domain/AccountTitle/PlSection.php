<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 損益計算書 (PL) における科目の所属セクション.
 *
 * 元実装の `JgaapAccountTitlePL.php` の各 root id に対応:
 *  - sales / salesSum                                   → Sales
 *  - costOfSales / costOfSalesSum                       → CostOfSales
 *  - sellingGeneralAndAdministrationExpenses[Sum]       → SellingAndAdmin
 *  - nonOperatingIncome[Sum]                            → NonOperatingIncome
 *  - nonOperatingExpenses[Sum]                          → NonOperatingExpenses
 *  - extraordinaryIncome[Sum]                           → ExtraordinaryIncome
 *  - extraordinaryLosses[Sum]                           → ExtraordinaryLosses
 *  - corporateInhabitantAndEnterpriseTax / corporateTaxAdjustments → Tax
 *
 * 計算ノード (grossProfitNet, operatingIncomeNet, ordinaryProfitNet,
 * currentTermProfitOrLossPreNet, currentTermProfitOrLossNet) は
 * セクション無し (PlSection は付与しない).
 */
enum PlSection: string
{
    case Sales = 'sales';
    case CostOfSales = 'costOfSales';
    case SellingAndAdmin = 'sellingAndAdmin';
    case NonOperatingIncome = 'nonOperatingIncome';
    case NonOperatingExpenses = 'nonOperatingExpenses';
    case ExtraordinaryIncome = 'extraordinaryIncome';
    case ExtraordinaryLosses = 'extraordinaryLosses';
    case Tax = 'tax';
}
