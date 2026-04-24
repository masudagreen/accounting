<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use Rucaro\Domain\Journal\JournalRepositoryInterface;

/**
 * Thin facade over {@see JournalRepositoryInterface::findByCriteria} that
 * keeps the controller layer from depending directly on the repository port.
 */
final readonly class SearchJournalUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
    ) {
    }

    public function execute(JournalSearchCriteria $criteria): JournalSearchResult
    {
        return $this->journals->findByCriteria($criteria);
    }
}
