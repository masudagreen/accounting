<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\CrSection;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\TrialBalance;

/**
 * 試算表から製造原価報告書を構築する.
 */
final class CostReportBuilder
{
    public static function build(AccountTree $tree, TrialBalance $tb): CostReport
    {
        $sums = [
            CrSection::Materials->value             => Money::zero(),
            CrSection::Labor->value                 => Money::zero(),
            CrSection::Manufacture->value           => Money::zero(),
            CrSection::OpeningWorkInProcess->value  => Money::zero(),
            CrSection::ClosingWorkInProcess->value  => Money::zero(),
            CrSection::RemoveTransfer->value        => Money::zero(),
        ];

        foreach ($tree->walk() as $node) {
            $section = $node->title()->crSection();
            if ($section === null) {
                continue;
            }
            $closing = $tb->closingBalance($node->title()->id());
            if ($closing->isZero()) {
                continue;
            }
            $sums[$section->value] = $sums[$section->value]->plus($closing);
        }

        return new CostReport(
            materials: $sums[CrSection::Materials->value],
            labor: $sums[CrSection::Labor->value],
            manufacture: $sums[CrSection::Manufacture->value],
            openingWorkInProcess: $sums[CrSection::OpeningWorkInProcess->value],
            closingWorkInProcess: $sums[CrSection::ClosingWorkInProcess->value],
            removeTransfer: $sums[CrSection::RemoveTransfer->value],
        );
    }
}
