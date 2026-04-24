<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

final readonly class JournalLineInput
{
    public function __construct(
        public string $side,
        public string $accountTitleId,
        public ?string $subAccountTitleId,
        public string $amount,
        public string $taxRatePercent,
        public string $taxAmount,
        public bool $isTaxReduced,
        public string $memo,
    ) {
    }
}
