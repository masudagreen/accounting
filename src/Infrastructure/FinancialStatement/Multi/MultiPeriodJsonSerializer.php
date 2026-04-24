<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Multi;

use DateTimeZone;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodSectionRow;
use Rucaro\Infrastructure\FinancialStatement\JsonFinancialStatementSerializer;

/**
 * Pure-function serializer that turns a {@see MultiPeriodFinancialStatement}
 * into the JSON envelope the HTTP layer ships.
 *
 * Shape:
 *   entityId, kind, generatedAt
 *   periods:         [ {fiscalTermId, label, fromDate, toDate, statement} ]
 *   comparison: { bs: [rows], pl: [rows], cs: [rows] }
 *
 * Each `row` carries `amounts` keyed by fiscalTermId so the client can render
 * the column order however it prefers — the use case already sorted `periods`
 * ascending by `fromDate`.
 */
final class MultiPeriodJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(MultiPeriodFinancialStatement $multi): array
    {
        $periods = [];
        foreach ($multi->periods as $entry) {
            $periods[] = [
                'fiscalTermId'    => $entry->fiscalTermId,
                'fiscalTermLabel' => $entry->fiscalTermLabel,
                'fromDate'        => $entry->fromDate->format('Y-m-d'),
                'toDate'          => $entry->toDate->format('Y-m-d'),
                'statement'       => JsonFinancialStatementSerializer::toArray($entry->statement),
            ];
        }

        return [
            'entityId'    => $multi->entityId,
            'kind'        => $multi->kind->value,
            'periods'     => $periods,
            'comparison'  => [
                'bs' => self::serializeRows(MultiPeriodRowBuilder::buildBs($multi)),
                'pl' => self::serializeRows(MultiPeriodRowBuilder::buildPl($multi)),
                'cs' => self::serializeRows(MultiPeriodRowBuilder::buildCs($multi)),
            ],
            'generatedAt' => $multi->generatedAt
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    /**
     * @param list<MultiPeriodSectionRow> $rows
     * @return list<array<string, mixed>>
     */
    private static function serializeRows(array $rows): array
    {
        return array_map(
            static fn (MultiPeriodSectionRow $row): array => [
                'sectionCode'     => $row->sectionCode,
                'lineCode'        => $row->lineCode,
                'label'           => $row->label,
                'amounts'         => $row->amounts,
                'variance'        => $row->variance,
                'variancePercent' => $row->variancePercent,
                'depth'           => $row->depth,
                'isSubtotal'      => $row->isSubtotal,
                'isTotal'         => $row->isTotal,
            ],
            $rows,
        );
    }
}
