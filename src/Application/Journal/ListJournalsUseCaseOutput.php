<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use Rucaro\Domain\Journal\Journal;

final readonly class ListJournalsUseCaseOutput
{
    /**
     * @param list<Journal> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $pageSize,
    ) {
    }
}
