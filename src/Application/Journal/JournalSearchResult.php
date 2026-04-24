<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use Rucaro\Domain\Journal\Journal;

/**
 * Paginated result envelope returned by {@see \Rucaro\Domain\Journal\JournalRepositoryInterface::findByCriteria}.
 */
final readonly class JournalSearchResult
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
