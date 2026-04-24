<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FixedAsset;

use Rucaro\Application\FixedAsset\GetFixedAssetLedgerOutput;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\FixedAsset;

/**
 * Serializes fixed-asset aggregates and ledgers to the JSON envelope
 * consumed by the HTTP controllers.
 */
final class FixedAssetJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(FixedAsset $asset): array
    {
        return [
            'id'                                       => $asset->id,
            'entityId'                                 => $asset->entityId,
            'assetCode'                                => $asset->assetCode,
            'assetName'                                => $asset->assetName,
            'categoryCode'                             => $asset->categoryCode,
            'assetAccountTitleId'                      => $asset->assetAccountTitleId,
            'accumulatedDepreciationAccountTitleId'    => $asset->accumulatedDepreciationAccountTitleId,
            'depreciationExpenseAccountTitleId'        => $asset->depreciationExpenseAccountTitleId,
            'acquisitionDate'                          => $asset->acquisitionDate->format('Y-m-d'),
            'serviceStartDate'                         => $asset->serviceStartDate->format('Y-m-d'),
            'disposalDate'                             => $asset->disposalDate?->format('Y-m-d'),
            'acquisitionCost'                          => $asset->acquisitionCost,
            'residualValue'                            => $asset->residualValue,
            'usefulLifeYears'                          => $asset->usefulLifeYears,
            'method'                                   => $asset->method->value,
            'quantity'                                 => $asset->quantity,
            'departmentCode'                           => $asset->departmentCode,
            'note'                                     => $asset->note,
            'createdBy'                                => $asset->createdBy,
            'createdAt'                                => $asset->createdAt->format(DATE_ATOM),
            'updatedAt'                                => $asset->updatedAt->format(DATE_ATOM),
            'deletedAt'                                => $asset->deletedAt?->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function scheduleEntryToArray(DepreciationScheduleEntry $e): array
    {
        return [
            'id'                       => $e->id,
            'fixedAssetId'             => $e->fixedAssetId,
            'fiscalTermId'             => $e->fiscalTermId,
            'periodNumber'             => $e->periodNumber,
            'periodStartDate'          => $e->periodStartDate->format('Y-m-d'),
            'periodEndDate'            => $e->periodEndDate->format('Y-m-d'),
            'monthsInService'          => $e->monthsInService,
            'openingBookValue'         => $e->openingBookValue,
            'depreciationAmount'       => $e->depreciationAmount,
            'accumulatedDepreciation'  => $e->accumulatedDepreciation,
            'closingBookValue'         => $e->closingBookValue,
            'isPosted'                 => $e->isPosted,
            'postedJournalEntryId'     => $e->postedJournalEntryId,
            'generatedAt'              => $e->generatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function ledgerToArray(GetFixedAssetLedgerOutput $output): array
    {
        return [
            'entityId'     => $output->entityId,
            'fiscalTermId' => $output->fiscalTermId,
            'generatedAt'  => $output->generatedAt->format(DATE_ATOM),
            'books'        => array_map(
                static fn (array $book): array => [
                    'asset'    => self::toArray($book['asset']),
                    'schedule' => array_map(
                        static fn (DepreciationScheduleEntry $e): array => self::scheduleEntryToArray($e),
                        $book['schedule'],
                    ),
                ],
                $output->books,
            ),
        ];
    }
}
