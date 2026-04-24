<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Budget;

use Rucaro\Domain\Budget\BudgetVarianceAnalysis;
use Rucaro\Domain\Budget\BudgetVarianceRow;

/**
 * Serializes {@see BudgetVarianceAnalysis} to the standard API envelope.
 */
final class BudgetVarianceJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(BudgetVarianceAnalysis $analysis): array
    {
        return [
            'budgetId'     => $analysis->budgetId,
            'entityId'     => $analysis->entityId,
            'fiscalTermId' => $analysis->fiscalTermId,
            'budgetName'   => $analysis->budgetName,
            'status'       => $analysis->status->value,
            'periodFrom'   => $analysis->periodFrom->format('Y-m-d'),
            'periodTo'     => $analysis->periodTo->format('Y-m-d'),
            'currencyCode' => $analysis->currencyCode,
            'rows'         => array_map([self::class, 'rowToArray'], $analysis->rows),
            'totals'       => [
                'budget'   => $analysis->totalBudget(),
                'actual'   => $analysis->totalActual(),
                'variance' => $analysis->totalVariance(),
            ],
            'generatedAt'  => $analysis->generatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rowToArray(BudgetVarianceRow $row): array
    {
        return [
            'accountTitleId'    => $row->accountTitleId,
            'accountTitleCode'  => $row->accountTitleCode,
            'accountTitleName'  => $row->accountTitleName,
            'budgetAmount'      => $row->budgetAmount,
            'actualAmount'      => $row->actualAmount,
            'varianceAmount'    => $row->varianceAmount,
            'usageRatePercent'  => $row->usageRatePercent,
            'isOverBudget'      => $row->isOverBudget(),
            'isUnderBudget'     => $row->isUnderBudget(),
        ];
    }
}
