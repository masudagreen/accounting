<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval\Service;

use DateTimeImmutable;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;

/**
 * Adapts a Journal draft into the generic {@see ApprovalTargetInterface}
 * contract consumed by the approval UseCases.
 *
 * State mutations go back through {@see JournalRepositoryInterface::save}
 * so the aggregate, its lines, and the approval side-effects are persisted
 * transactionally by the existing {@see \Rucaro\Infrastructure\Journal\PdoJournalRepository}.
 */
final class JournalApprovalTarget implements ApprovalTargetInterface
{
    public function __construct(
        private Journal $journal,
        private readonly JournalRepositoryInterface $journals,
    ) {
    }

    public function kind(): ApprovalTargetKind
    {
        return ApprovalTargetKind::Journal;
    }

    public function id(): string
    {
        return $this->journal->id;
    }

    public function summary(): string
    {
        $summary = trim($this->journal->summary);
        if ($summary === '') {
            return sprintf('Journal %s', $this->journal->id);
        }
        return $summary;
    }

    public function details(): array
    {
        return [
            'journal_id'    => $this->journal->id,
            'journal_date'  => $this->journal->journalDate->format('Y-m-d'),
            'total_amount'  => $this->journal->totalAmount,
            'currency_code' => $this->journal->currencyCode,
            'status'        => $this->journal->status,
            'source'        => $this->journal->source,
            'lines'         => array_map(
                static fn ($line): array => [
                    'line_no'          => $line->lineNo,
                    'side'             => $line->side,
                    'account_title_id' => $line->accountTitleId,
                    'amount'           => $line->amount,
                    'memo'             => $line->memo,
                ],
                $this->journal->lines,
            ),
        ];
    }

    public function applyApproval(string $actorUserId, DateTimeImmutable $at): void
    {
        $approved = $this->journal->approve($at, $actorUserId);
        $this->journals->save($approved);
        $this->journal = $approved;
    }

    public function applyRejection(string $actorUserId, DateTimeImmutable $at, string $reason): void
    {
        $rejected = $this->journal->reject($at, $actorUserId, $reason);
        $this->journals->save($rejected);
        $this->journal = $rejected;
    }

    /**
     * Current aggregate snapshot; exposed primarily for tests that want to
     * observe the transition without re-reading the repository.
     */
    public function currentJournal(): Journal
    {
        return $this->journal;
    }
}
