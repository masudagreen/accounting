<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BreakEvenPoint;

use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;

/**
 * Serializers for the Break-Even Point HTTP responses.
 */
final class BreakEvenPointJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function analysisToArray(BreakEvenPointAnalysis $a): array
    {
        return [
            'entityId'                => $a->entityId,
            'fiscalTermId'            => $a->fiscalTermId,
            'fromDate'                => $a->fromDate->format('Y-m-d'),
            'toDate'                  => $a->toDate->format('Y-m-d'),
            'currencyCode'            => $a->currencyCode,
            'sales'                   => $a->sales,
            'variableCosts'           => $a->variableCosts,
            'fixedCosts'              => $a->fixedCosts,
            'contributionMargin'      => $a->contributionMargin,
            'contributionMarginRate'  => $a->contributionMarginRate,
            'bepSales'                => $a->bepSales,
            'bepRatio'                => $a->bepRatio,
            'safetyMarginRatio'       => $a->safetyMarginRatio,
            'operatingProfit'         => $a->operatingProfit,
            'salesBreakdown'          => $a->salesBreakdown,
            'variableBreakdown'       => $a->variableBreakdown,
            'fixedBreakdown'          => $a->fixedBreakdown,
            'isBelowBreakEven'        => $a->isBelowBreakEven(),
            'generatedAt'             => $a->generatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function classificationToArray(AccountTitleCvpClassification $c): array
    {
        return [
            'entityId'        => $c->entityId,
            'accountTitleId'  => $c->accountTitleId,
            'costType'        => $c->costType->value,
            'variableRatio'   => $c->variableRatio,
            'notes'           => $c->notes,
        ];
    }

    /**
     * @param list<AccountTitleCvpClassification> $rows
     * @return list<array<string, mixed>>
     */
    public static function classificationsToArray(array $rows): array
    {
        return array_values(array_map([self::class, 'classificationToArray'], $rows));
    }
}
