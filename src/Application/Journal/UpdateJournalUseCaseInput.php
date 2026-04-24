<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

final readonly class UpdateJournalUseCaseInput
{
    /**
     * @param list<JournalLineInput> $lines
     */
    public function __construct(
        public string $journalId,
        public string $updatedBy,
        public array $lines,
        public ?string $summary = null,
    ) {
    }
}
