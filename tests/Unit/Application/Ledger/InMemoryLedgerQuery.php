<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Ledger;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Domain\Ledger\LedgerQueryInterface;

/**
 * In-memory {@see LedgerQueryInterface} for UseCase-level tests.
 *
 * Driven by a list of posted journal lines; groups them by entry to resolve
 * counter accounts, then sorts and emits a {@see Ledger} whose books carry
 * placeholder openingBalance / runningBalance fields (0). The use case
 * recomputes the final book with the real opening balance via the
 * OpeningBalanceRepositoryInterface, which mirrors the production contract.
 */
final class InMemoryLedgerQuery implements LedgerQueryInterface
{
    /** @var list<array{
     *     entityId:string, fiscalTermId:string, date:DateTimeImmutable,
     *     entryId:string, lineId:string, lineNo:int, side:string, amount:string,
     *     accountId:string, accountCode:string, accountName:string, normalSide:string,
     *     summary:string, memo:string,
     * }>
     */
    private array $lines = [];

    /** @var array<string, array{code:string, name:string, normalSide:string}> */
    private array $accounts = [];

    public function registerAccount(string $id, string $code, string $name, string $normalSide): void
    {
        $this->accounts[$id] = ['code' => $code, 'name' => $name, 'normalSide' => $normalSide];
    }

    public function addLine(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $date,
        string $entryId,
        string $lineId,
        int $lineNo,
        string $side,
        string $amount,
        string $accountId,
        string $summary = '',
        string $memo = '',
    ): void {
        $acc = $this->accounts[$accountId] ?? ['code' => '', 'name' => '', 'normalSide' => 'debit'];
        $this->lines[] = [
            'entityId'     => $entityId,
            'fiscalTermId' => $fiscalTermId,
            'date'         => $date,
            'entryId'      => $entryId,
            'lineId'       => $lineId,
            'lineNo'       => $lineNo,
            'side'         => $side,
            'amount'       => $amount,
            'accountId'    => $accountId,
            'accountCode'  => $acc['code'],
            'accountName'  => $acc['name'],
            'normalSide'   => $acc['normalSide'],
            'summary'      => $summary,
            'memo'         => $memo,
        ];
    }

    public function query(
        string $entityId,
        string $fiscalTermId,
        ?string $accountTitleId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): Ledger {
        $filtered = array_filter(
            $this->lines,
            static fn (array $l): bool => $l['entityId'] === $entityId
                && $l['fiscalTermId'] === $fiscalTermId
                && $l['date'] >= $from
                && $l['date'] <= $to,
        );

        /** @var array<string, list<array<string, mixed>>> $byEntry */
        $byEntry = [];
        foreach ($filtered as $line) {
            $byEntry[$line['entryId']][] = $line;
        }

        /** @var array<string, list<array{
         *     journalEntryId: string,
         *     journalEntryLineId: string,
         *     entryDate: DateTimeImmutable,
         *     summary: string,
         *     memo: string,
         *     counterAccountCode: string,
         *     counterAccountName: string,
         *     debitAmount: string,
         *     creditAmount: string,
         * }>> $entriesByAccount
         */
        $entriesByAccount = [];
        foreach ($filtered as $line) {
            if ($accountTitleId !== null && $line['accountId'] !== $accountTitleId) {
                continue;
            }
            $others = array_values(array_filter(
                $byEntry[$line['entryId']] ?? [],
                static fn (array $l): bool => $l['accountId'] !== $line['accountId'],
            ));
            $counterCode = '';
            $counterName = '';
            if (count($others) === 1) {
                /** @var array<string, mixed> $only */
                $only = $others[0];
                $counterCode = (string) $only['accountCode'];
                $counterName = (string) $only['accountName'];
            } elseif (count($others) > 1) {
                $counterName = LedgerEntry::COUNTER_SUNDRIES;
            }
            $entriesByAccount[$line['accountId']] ??= [];
            $entriesByAccount[$line['accountId']][] = [
                'journalEntryId'     => $line['entryId'],
                'journalEntryLineId' => $line['lineId'],
                'entryDate'          => $line['date'],
                'summary'            => $line['summary'],
                'memo'               => $line['memo'],
                'counterAccountCode' => $counterCode,
                'counterAccountName' => $counterName,
                'debitAmount'        => $line['side'] === 'debit' ? $line['amount'] : '0',
                'creditAmount'       => $line['side'] === 'credit' ? $line['amount'] : '0',
            ];
        }

        $books = [];
        $candidateIds = $accountTitleId !== null ? [$accountTitleId] : array_keys($this->accounts);
        // sort by account code
        usort(
            $candidateIds,
            fn (string $a, string $b): int
                => strcmp($this->accounts[$a]['code'] ?? '', $this->accounts[$b]['code'] ?? ''),
        );
        foreach ($candidateIds as $id) {
            $meta = $this->accounts[$id] ?? null;
            if ($meta === null) {
                continue;
            }
            $raw = $entriesByAccount[$id] ?? [];
            // Sort entries within a book by entryDate then entryId then lineId
            usort(
                $raw,
                static function (array $x, array $y): int {
                    $cmp = $x['entryDate'] <=> $y['entryDate'];
                    if ($cmp !== 0) {
                        return $cmp;
                    }
                    $cmp = strcmp($x['journalEntryId'], $y['journalEntryId']);
                    if ($cmp !== 0) {
                        return $cmp;
                    }
                    return strcmp($x['journalEntryLineId'], $y['journalEntryLineId']);
                },
            );
            $books[] = LedgerBook::compute(
                accountTitleId: $id,
                accountTitleCode: $meta['code'],
                accountTitleName: $meta['name'],
                normalSide: $meta['normalSide'],
                openingBalance: '0',
                rawEntries: $raw,
            );
        }

        return new Ledger(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $from,
            toDate: $to,
            currencyCode: 'JPY',
            books: $books,
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }
}
