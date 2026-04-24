<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeZone;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Replaces the line collection on an existing draft journal.
 *
 * Only entries still in `draft` status may be updated; the aggregate raises
 * {@see InvariantViolationException} otherwise via {@see Journal::withLines}.
 */
final readonly class UpdateJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateJournalUseCaseInput $input): Journal
    {
        $existing = $this->journals->findById($input->journalId);
        if ($existing === null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $input->journalId));
        }

        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));

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
                bookedAt: $now,
            );
            $lineNo++;
        }

        $updated = $existing->withLines($lines);
        // Refresh updatedAt on the header regardless of whether any other
        // header-level fields changed, so stale caches downstream invalidate.
        $refreshed = new Journal(
            id: $updated->id,
            entityId: $updated->entityId,
            fiscalTermId: $updated->fiscalTermId,
            journalDate: $updated->journalDate,
            bookedAt: $updated->bookedAt,
            summary: $input->summary ?? $updated->summary,
            totalAmount: $updated->totalAmount,
            currencyCode: $updated->currencyCode,
            status: $updated->status,
            source: $updated->source,
            sourceReceiptId: $updated->sourceReceiptId,
            createdBy: $updated->createdBy,
            approvedBy: $updated->approvedBy,
            approvedAt: $updated->approvedAt,
            createdAt: $updated->createdAt,
            updatedAt: $now,
            deletedAt: $updated->deletedAt,
            lines: $updated->lines,
        );

        $this->journals->save($refreshed);
        return $refreshed;
    }
}
