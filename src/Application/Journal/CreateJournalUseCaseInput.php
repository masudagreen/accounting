<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

final readonly class CreateJournalUseCaseInput
{
    /**
     * @param list<JournalLineInput> $lines
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public \DateTimeImmutable $journalDate,
        public string $summary,
        public string $source,
        public ?string $sourceReceiptId,
        public string $currencyCode,
        public string $createdBy,
        public array $lines,
        /**
         * Admin-only fast-path that skips draft → approved → posted and
         * persists the new aggregate as `posted` directly. The caller is
         * responsible for the role check; the use case trusts the flag.
         */
        public bool $skipApproval = false,
    ) {
    }
}
