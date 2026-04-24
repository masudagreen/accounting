<?php

declare(strict_types=1);

namespace Rucaro\Application\Ledger;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerQueryInterface;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Produce a {@see Ledger} for an entity + fiscal term over a period.
 *
 * The use case:
 *   1. Defers date resolution to the caller. {@see QueryLedgerUseCaseInput::$fromDate}
 *      / `$toDate` must be populated; the HTTP controller layer fills in the
 *      fiscal term's start / end when query params are missing.
 *   2. Delegates the raw projection to {@see LedgerQueryInterface}.
 *   3. Recomputes each book with its opening balance (sourced from
 *      {@see OpeningBalanceRepositoryInterface}) so the running balance
 *      reflects carry-forward correctly.
 *
 * The query service is expected to populate each book with entries already
 * sorted and with placeholder opening balance / running balance fields; this
 * use case discards those placeholders and builds final {@see LedgerBook}
 * instances via {@see LedgerBook::compute()}.
 */
final readonly class QueryLedgerUseCase
{
    public function __construct(
        private LedgerQueryInterface $query,
        private OpeningBalanceRepositoryInterface $openingBalances,
        private ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function execute(QueryLedgerUseCaseInput $input): QueryLedgerUseCaseOutput
    {
        if ($input->fromDate === null || $input->toDate === null) {
            throw new \InvalidArgumentException(
                'QueryLedgerUseCase requires explicit fromDate and toDate; '
                . 'the HTTP controller resolves fiscal term bounds before calling the use case.',
            );
        }

        $projection = $this->query->query(
            $input->entityId,
            $input->fiscalTermId,
            $input->accountTitleId,
            $input->fromDate,
            $input->toDate,
        );

        $books = [];
        foreach ($projection->books as $raw) {
            $opening = $this->openingBalances->findOpeningBalance(
                $input->entityId,
                $input->fiscalTermId,
                $raw->accountTitleId,
            );
            $books[] = LedgerBook::compute(
                accountTitleId: $raw->accountTitleId,
                accountTitleCode: $raw->accountTitleCode,
                accountTitleName: $raw->accountTitleName,
                normalSide: $raw->normalSide,
                openingBalance: $opening,
                rawEntries: array_map(
                    static fn ($e): array => [
                        'journalEntryId'     => $e->journalEntryId,
                        'journalEntryLineId' => $e->journalEntryLineId,
                        'entryDate'          => $e->entryDate,
                        'summary'            => $e->summary,
                        'memo'               => $e->memo,
                        'counterAccountCode' => $e->counterAccountCode,
                        'counterAccountName' => $e->counterAccountName,
                        'debitAmount'        => $e->debitAmount,
                        'creditAmount'       => $e->creditAmount,
                    ],
                    $raw->entries,
                ),
            );
        }

        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));

        return new QueryLedgerUseCaseOutput(
            new Ledger(
                entityId: $input->entityId,
                fiscalTermId: $input->fiscalTermId,
                fromDate: $input->fromDate,
                toDate: $input->toDate,
                currencyCode: $input->currencyCode,
                books: $books,
                generatedAt: $generatedAt,
            ),
        );
    }
}
