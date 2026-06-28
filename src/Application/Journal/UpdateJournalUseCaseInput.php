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
        /**
         * Admin escape hatch: when true, edit even non-draft journals
         * (posted etc.). Caller is responsible for the role check.
         */
        public bool $bypassMutabilityCheck = false,
    ) {
    }
}
