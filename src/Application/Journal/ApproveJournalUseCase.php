<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeZone;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Transitions a draft or pending journal to Approved.
 *
 * Pure orchestration: the aggregate enforces the legal transition, this
 * use case only threads the clock + persistence together.
 */
final readonly class ApproveJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $journalId, string $approvedBy): Journal
    {
        $existing = $this->journals->findById($journalId);
        if ($existing === null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $journalId));
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $approved = $existing->approve($now, $approvedBy);
        $this->journals->save($approved);
        return $approved;
    }
}
