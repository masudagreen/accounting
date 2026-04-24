<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeZone;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Soft-deletes a draft journal.
 *
 * Posted or approved entries never go away — the ledger must stay
 * reconstructible — so callers get an {@see InvariantViolationException}
 * if they try to delete anything other than a Draft.
 */
final readonly class DeleteJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $journalId, string $deletedBy): void
    {
        $existing = $this->journals->findById($journalId);
        if ($existing === null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $journalId));
        }
        if (!$existing->statusEnum()->isMutable()) {
            throw InvariantViolationException::for('journal.cannot_delete_non_draft', [
                'status' => $existing->status,
            ]);
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $this->journals->delete($existing->id, $now, $deletedBy);
    }
}
