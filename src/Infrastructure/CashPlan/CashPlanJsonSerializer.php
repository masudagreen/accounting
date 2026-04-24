<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\CashPlan;

use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanEntry;

/**
 * Serializes CashPlan aggregates to the standard response envelope.
 */
final class CashPlanJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(CashPlan $plan): array
    {
        $deltas = [];
        $closings = [];
        for ($m = 1; $m <= CashPlanEntry::MONTHS; $m++) {
            $deltas['month_' . $m] = $plan->monthlyDelta($m);
            $closings['month_' . $m] = $plan->closingBalance($m);
        }
        return [
            'id'             => $plan->id,
            'entityId'       => $plan->entityId,
            'fiscalTermId'   => $plan->fiscalTermId,
            'name'           => $plan->name,
            'openingBalance' => $plan->openingBalance,
            'currencyCode'   => $plan->currencyCode,
            'notes'          => $plan->notes,
            'entries'        => array_map([self::class, 'entryToArray'], $plan->entries),
            'totals'         => $plan->totalsByCategory(),
            'monthlyDeltas'  => $deltas,
            'closingBalances' => $closings,
            'createdBy'      => $plan->createdBy,
            'createdAt'      => $plan->createdAt->format(DATE_ATOM),
            'updatedAt'      => $plan->updatedAt->format(DATE_ATOM),
            'deletedAt'      => $plan->deletedAt?->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<CashPlan> $plans
     * @return list<array<string, mixed>>
     */
    public static function toArrayList(array $plans): array
    {
        return array_values(array_map([self::class, 'toArray'], $plans));
    }

    /**
     * @return array<string, mixed>
     */
    public static function entryToArray(CashPlanEntry $entry): array
    {
        $months = [];
        for ($i = 1; $i <= CashPlanEntry::MONTHS; $i++) {
            $months['month_' . $i] = $entry->monthlyAmounts[$i - 1];
        }
        return [
            'id'             => $entry->id,
            'category'       => $entry->category->value,
            'label'          => $entry->label,
            'sortOrder'      => $entry->sortOrder,
            'monthlyAmounts' => $months,
            'total'          => $entry->total(),
            'memo'           => $entry->memo,
        ];
    }
}
