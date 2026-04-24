<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

final readonly class ReverseJournalUseCaseInput
{
    public function __construct(
        public string $journalId,
        public string $reversedBy,
        public string $reason,
    ) {
    }
}
