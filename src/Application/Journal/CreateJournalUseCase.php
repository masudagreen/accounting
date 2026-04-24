<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a new {@see Journal} aggregate and persist it.
 *
 * Balance invariant is checked inside the aggregate itself via
 * {@see Journal::balance()}; this use case is responsible for:
 *   - Allocating ULIDs for the entry and each line
 *   - Stamping `bookedAt`, `createdAt`, `updatedAt` consistently in UTC
 *   - Delegating to the repository, which wraps the write in a transaction
 */
final readonly class CreateJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateJournalUseCaseInput $input): Journal
    {
        if (count($input->lines) < 2) {
            throw ValidationException::withErrors([
                'lines' => ['at least 2 lines are required (one debit, one credit)'],
            ]);
        }

        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $bookedAt = $now;

        /** @var list<JournalLine> $lines */
        $lines = [];
        $lineNo = 1;
        foreach ($input->lines as $raw) {
            $lines[] = new JournalLine(
                id: $this->ulids->generate(),
                lineNo: $lineNo,
                side: $raw->side,
                accountTitleId: $raw->accountTitleId,
                subAccountTitleId: $raw->subAccountTitleId,
                amount: $raw->amount,
                taxRatePercent: $raw->taxRatePercent,
                taxAmount: $raw->taxAmount,
                isTaxReduced: $raw->isTaxReduced,
                memo: $raw->memo,
                bookedAt: $bookedAt,
            );
            $lineNo++;
        }

        $total = Journal::balance($lines);

        $journal = new Journal(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            journalDate: $input->journalDate,
            bookedAt: $bookedAt,
            summary: $input->summary,
            totalAmount: $total,
            currencyCode: $input->currencyCode,
            status: 'draft',
            source: $input->source,
            sourceReceiptId: $input->sourceReceiptId,
            createdBy: $input->createdBy,
            approvedBy: null,
            approvedAt: null,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
            lines: $lines,
        );

        $this->journals->save($journal);
        return $journal;
    }
}
