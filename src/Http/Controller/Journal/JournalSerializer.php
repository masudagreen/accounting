<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use DateTimeZone;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;

/**
 * Shared conversion from {@see Journal} aggregate to the API response shape.
 *
 * Centralising it avoids drift between the list and create controllers.
 */
final class JournalSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Journal $j): array
    {
        return [
            'id'             => $j->id,
            'entityId'       => $j->entityId,
            'fiscalTermId'   => $j->fiscalTermId,
            'journalDate'    => $j->journalDate->format('Y-m-d'),
            'bookedAt'       => $j->bookedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'summary'        => $j->summary,
            'totalAmount'    => $j->totalAmount,
            'currencyCode'   => $j->currencyCode,
            'status'         => $j->status,
            'source'         => $j->source,
            'sourceReceiptId' => $j->sourceReceiptId,
            'createdBy'      => $j->createdBy,
            'approvedBy'     => $j->approvedBy,
            'approvedAt'     => $j->approvedAt?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d\TH:i:s.u\Z'),
            'createdAt'      => $j->createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'updatedAt'      => $j->updatedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'deletedAt'      => $j->deletedAt?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d\TH:i:s.u\Z'),
            'lines'          => array_map(
                static fn (JournalLine $l): array => [
                    'id'                 => $l->id,
                    'lineNo'             => $l->lineNo,
                    'side'               => $l->side,
                    'accountTitleId'     => $l->accountTitleId,
                    'subAccountTitleId'  => $l->subAccountTitleId,
                    'amount'             => $l->amount,
                    'taxRatePercent'     => $l->taxRatePercent,
                    'taxAmount'          => $l->taxAmount,
                    'isTaxReduced'       => $l->isTaxReduced,
                    'memo'               => $l->memo,
                    'bookedAt'           => $l->bookedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
                ],
                $j->lines,
            ),
        ];
    }
}
