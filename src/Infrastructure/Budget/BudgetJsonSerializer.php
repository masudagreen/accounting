<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Budget;

use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;

/**
 * Serializes {@see Budget} aggregates to the standard API envelope.
 */
final class BudgetJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Budget $budget): array
    {
        $monthlyTotals = [];
        for ($m = 1; $m <= BudgetLineItem::MONTHS; $m++) {
            $monthlyTotals['month_' . $m] = $budget->monthlyTotal($m);
        }
        return [
            'id'            => $budget->id,
            'entityId'      => $budget->entityId,
            'fiscalTermId'  => $budget->fiscalTermId,
            'name'          => $budget->name,
            'status'        => $budget->status->value,
            'approvedBy'    => $budget->approvedBy,
            'approvedAt'    => $budget->approvedAt?->format(DATE_ATOM),
            'notes'         => $budget->notes,
            'lineItems'     => array_map([self::class, 'lineItemToArray'], $budget->lineItems),
            'monthlyTotals' => $monthlyTotals,
            'annualTotal'   => $budget->annualTotal(),
            'createdBy'     => $budget->createdBy,
            'createdAt'     => $budget->createdAt->format(DATE_ATOM),
            'updatedAt'     => $budget->updatedAt->format(DATE_ATOM),
            'deletedAt'     => $budget->deletedAt?->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<Budget> $budgets
     * @return list<array<string, mixed>>
     */
    public static function toArrayList(array $budgets): array
    {
        return array_values(array_map([self::class, 'toArray'], $budgets));
    }

    /**
     * @return array<string, mixed>
     */
    public static function lineItemToArray(BudgetLineItem $li): array
    {
        $months = [];
        for ($m = 1; $m <= BudgetLineItem::MONTHS; $m++) {
            $months['month_' . $m] = $li->monthlyAmounts[$m - 1];
        }
        return [
            'id'                 => $li->id,
            'accountTitleId'     => $li->accountTitleId,
            'subAccountTitleId'  => $li->subAccountTitleId,
            'sortOrder'          => $li->sortOrder,
            'monthlyAmounts'     => $months,
            'total'              => $li->totalAmount(),
            'memo'               => $li->memo,
        ];
    }
}
