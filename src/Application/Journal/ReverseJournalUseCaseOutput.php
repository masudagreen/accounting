<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use Rucaro\Domain\Journal\Journal;

final readonly class ReverseJournalUseCaseOutput
{
    public function __construct(
        public Journal $source,
        public Journal $reversal,
    ) {
    }
}
