<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeZone;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Finalises an approved journal (Approved -> Posted).
 *
 * Posting is the point at which the journal becomes immutable and is
 * reflected in downstream reports (trial balance, aged AR, etc.).
 */
final readonly class PostJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $journalId, string $postedBy): Journal
    {
        $existing = $this->journals->findById($journalId);
        if ($existing === null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $journalId));
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $posted = $existing->post($now, $postedBy);
        $this->journals->save($posted);
        return $posted;
    }
}
