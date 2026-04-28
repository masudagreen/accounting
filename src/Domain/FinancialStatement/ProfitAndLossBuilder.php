<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\PlSection;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\TrialBalance;

/**
 * 試算表から損益計算書を構築する.
 *
 * 各 PL 科目 (PlSection が付与された AccountTitle) の期末残高を
 * セクション別に合計する.
 */
final class ProfitAndLossBuilder
{
    public static function build(AccountTree $tree, TrialBalance $tb): ProfitAndLossStatement
    {
        $sums = [
            PlSection::Sales->value                  => Money::zero(),
            PlSection::CostOfSales->value            => Money::zero(),
            PlSection::SellingAndAdmin->value        => Money::zero(),
            PlSection::NonOperatingIncome->value     => Money::zero(),
            PlSection::NonOperatingExpenses->value   => Money::zero(),
            PlSection::ExtraordinaryIncome->value    => Money::zero(),
            PlSection::ExtraordinaryLosses->value    => Money::zero(),
            PlSection::Tax->value                    => Money::zero(),
        ];

        foreach ($tree->walk() as $node) {
            $section = $node->title()->plSection();
            if ($section === null) {
                continue;
            }
            $closing = $tb->closingBalance($node->title()->id());
            if ($closing->isZero()) {
                continue;
            }
            $sums[$section->value] = $sums[$section->value]->plus($closing);
        }

        return new ProfitAndLossStatement(
            sales: $sums[PlSection::Sales->value],
            costOfSales: $sums[PlSection::CostOfSales->value],
            sellingAndAdmin: $sums[PlSection::SellingAndAdmin->value],
            nonOperatingIncome: $sums[PlSection::NonOperatingIncome->value],
            nonOperatingExpenses: $sums[PlSection::NonOperatingExpenses->value],
            extraordinaryIncome: $sums[PlSection::ExtraordinaryIncome->value],
            extraordinaryLosses: $sums[PlSection::ExtraordinaryLosses->value],
            tax: $sums[PlSection::Tax->value],
        );
    }
}
