<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

final readonly class ListJournalsUseCaseInput
{
    public function __construct(
        public string $entityId,
        /** @var int<1, max> */
        public int $page,
        /** @var int<1, max> */
        public int $pageSize,
        public ?string $fiscalTermId = null,
        public ?string $from = null,
        public ?string $to = null,
        public ?string $status = null,
        public ?string $source = null,
        public ?string $search = null,
        public bool $includeTrashed = false,
    ) {
    }
}
