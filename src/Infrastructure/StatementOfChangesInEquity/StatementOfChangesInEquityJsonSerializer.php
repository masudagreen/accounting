<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\StatementOfChangesInEquity;

use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSection;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;

/**
 * Serializes {@see StatementOfChangesInEquity} and
 * {@see SsManualAdjustment} aggregates into the standard envelope
 * shape expected by the HTTP layer.
 */
final class StatementOfChangesInEquityJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function statementToArray(StatementOfChangesInEquity $ss): array
    {
        return [
            'entityId'     => $ss->entityId,
            'fiscalTermId' => $ss->fiscalTermId,
            'fromDate'     => $ss->fromDate->format('Y-m-d'),
            'toDate'       => $ss->toDate->format('Y-m-d'),
            'currencyCode' => $ss->currencyCode,
            'sections'     => array_map([self::class, 'sectionToArray'], $ss->sections),
            'totals'       => $ss->totals(),
            'generatedAt'  => $ss->generatedAt->format('Y-m-d\TH:i:sP'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function sectionToArray(SsSection $section): array
    {
        return [
            'sectionCode'    => $section->sectionCode->value,
            'label'          => $section->label,
            'openingBalance' => $section->openingBalance,
            'changes'        => array_map([self::class, 'changeToArray'], $section->changes),
            'endingBalance'  => $section->endingBalance,
            'totalChange'    => $section->totalChange(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function changeToArray(SsChange $change): array
    {
        return [
            'changeTypeCode' => $change->changeType->value,
            'label'          => $change->label,
            'amount'         => $change->amount,
            'source'         => $change->source,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function adjustmentToArray(SsManualAdjustment $a): array
    {
        return [
            'id'             => $a->id,
            'entityId'       => $a->entityId,
            'fiscalTermId'   => $a->fiscalTermId,
            'sectionCode'    => $a->sectionCode->value,
            'changeTypeCode' => $a->changeType->value,
            'amount'         => $a->amount,
            'label'          => $a->label,
            'sortOrder'      => $a->sortOrder,
            'notes'          => $a->notes,
        ];
    }

    /**
     * @param list<SsManualAdjustment> $adjustments
     * @return list<array<string, mixed>>
     */
    public static function adjustmentListToArray(array $adjustments): array
    {
        return array_values(array_map([self::class, 'adjustmentToArray'], $adjustments));
    }
}
