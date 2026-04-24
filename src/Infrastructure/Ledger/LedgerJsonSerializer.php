<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ledger;

use DateTimeZone;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;

/**
 * Pure-function serializer that turns a {@see Ledger} aggregate into the
 * array shape the API envelope wraps under `data`.
 *
 * Kept separate from the HTTP controller so tests can assert on the
 * shape without booting FastRoute or dompdf.
 *
 * Output shape:
 *   entityId, fiscalTermId, fromDate, toDate, currencyCode, generatedAt,
 *   books: [
 *     accountTitleId, accountTitleCode, accountTitleName, normalSide,
 *     openingBalance, debitTotal, creditTotal, closingBalance,
 *     entries: [ { journalEntryId, journalEntryLineId, entryDate, summary,
 *                  memo, counterAccountCode, counterAccountName,
 *                  debitAmount, creditAmount, runningBalance } ]
 *   ]
 */
final class LedgerJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Ledger $ledger): array
    {
        return [
            'entityId'     => $ledger->entityId,
            'fiscalTermId' => $ledger->fiscalTermId,
            'fromDate'     => $ledger->fromDate->format('Y-m-d'),
            'toDate'       => $ledger->toDate->format('Y-m-d'),
            'currencyCode' => $ledger->currencyCode,
            'books'        => array_map(
                static fn (LedgerBook $b): array => self::book($b),
                $ledger->books,
            ),
            'generatedAt'  => $ledger->generatedAt
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function book(LedgerBook $b): array
    {
        return [
            'accountTitleId'   => $b->accountTitleId,
            'accountTitleCode' => $b->accountTitleCode,
            'accountTitleName' => $b->accountTitleName,
            'normalSide'       => $b->normalSide,
            'openingBalance'   => $b->openingBalance,
            'debitTotal'       => $b->debitTotal,
            'creditTotal'      => $b->creditTotal,
            'closingBalance'   => $b->closingBalance,
            'entries'          => array_map(
                static fn (LedgerEntry $e): array => self::entry($e),
                $b->entries,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function entry(LedgerEntry $e): array
    {
        return [
            'journalEntryId'     => $e->journalEntryId,
            'journalEntryLineId' => $e->journalEntryLineId,
            'entryDate'          => $e->entryDate->format('Y-m-d'),
            'summary'            => $e->summary,
            'memo'               => $e->memo,
            'counterAccountCode' => $e->counterAccountCode,
            'counterAccountName' => $e->counterAccountName,
            'debitAmount'        => $e->debitAmount,
            'creditAmount'       => $e->creditAmount,
            'runningBalance'     => $e->runningBalance,
        ];
    }
}
