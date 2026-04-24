<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

final readonly class PostDepreciationJournalInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public string $postedBy,
    ) {
    }
}
