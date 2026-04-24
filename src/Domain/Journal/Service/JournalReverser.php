<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\Service;

use DateTimeImmutable;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Domain service that produces a reversing journal entry from a source
 * {@see Journal}.
 *
 * Reversal is a structural operation — every line's `side` is flipped so
 * the aggregate balance stays zero when the reversal is booked alongside
 * the original. Totals, tax amounts and memos are carried through
 * unchanged; the summary is prefixed so reports can flag reversals at a
 * glance.
 */
final class JournalReverser
{
    public function __construct(
        private readonly UlidGenerator $ulids,
    ) {
    }

    public function reverse(
        Journal $source,
        DateTimeImmutable $reversedAt,
        string $reversedBy,
        string $reason,
    ): Journal {
        /** @var list<JournalLine> $reversedLines */
        $reversedLines = [];
        $lineNo = 1;
        foreach ($source->lines as $line) {
            $reversedLines[] = new JournalLine(
                id: $this->ulids->generate(),
                lineNo: $lineNo,
                side: $line->isDebit() ? JournalLine::SIDE_CREDIT : JournalLine::SIDE_DEBIT,
                accountTitleId: $line->accountTitleId,
                subAccountTitleId: $line->subAccountTitleId,
                amount: $line->amount,
                taxRatePercent: $line->taxRatePercent,
                taxAmount: $line->taxAmount,
                isTaxReduced: $line->isTaxReduced,
                memo: $line->memo,
                bookedAt: $reversedAt,
            );
            $lineNo++;
        }

        $summary = trim(sprintf('[REVERSED:%s] %s', $reason, $source->summary));

        return new Journal(
            id: $this->ulids->generate(),
            entityId: $source->entityId,
            fiscalTermId: $source->fiscalTermId,
            journalDate: $reversedAt,
            bookedAt: $reversedAt,
            summary: $summary,
            totalAmount: $source->totalAmount,
            currencyCode: $source->currencyCode,
            status: 'posted',
            source: $source->source,
            sourceReceiptId: $source->sourceReceiptId,
            createdBy: $reversedBy,
            approvedBy: $reversedBy,
            approvedAt: $reversedAt,
            createdAt: $reversedAt,
            updatedAt: $reversedAt,
            deletedAt: null,
            lines: $reversedLines,
        );
    }
}
