<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeZone;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Domain\Journal\Service\JournalReverser;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Books a reversing journal against an already-posted entry.
 *
 * Two writes happen in sequence: the source entry flips to `reversed` and a
 * fresh journal with swapped debit/credit lines is persisted. The PDO
 * repository wraps each `save` in its own transaction; callers that want
 * atomic both-or-neither semantics should wrap the pair in a Unit-of-Work
 * (tracked as a Phase 4.3 item).
 */
final readonly class ReverseJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private JournalReverser $reverser,
        private ClockInterface $clock,
    ) {
    }

    public function execute(ReverseJournalUseCaseInput $input): ReverseJournalUseCaseOutput
    {
        $source = $this->journals->findById($input->journalId);
        if ($source === null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $input->journalId));
        }

        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $reversedSource = $source->reverse($now, $input->reversedBy, $input->reason);
        $reversalEntry = $this->reverser->reverse($source, $now, $input->reversedBy, $input->reason);

        $this->journals->save($reversedSource);
        $this->journals->save($reversalEntry);

        return new ReverseJournalUseCaseOutput(
            source: $reversedSource,
            reversal: $reversalEntry,
        );
    }
}
