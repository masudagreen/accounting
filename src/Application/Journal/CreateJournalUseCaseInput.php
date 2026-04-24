<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use DateTimeImmutable;

final readonly class CreateJournalUseCaseInput
{
    /**
     * @param list<JournalLineInput> $lines
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $journalDate,
        public string $summary,
        public string $source,
        public ?string $sourceReceiptId,
        public string $currencyCode,
        public string $createdBy,
        public array $lines,
    ) {
    }
}
