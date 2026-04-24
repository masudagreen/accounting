<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

final readonly class GetFixedAssetLedgerInput
{
    public function __construct(
        public string $entityId,
        public ?string $fiscalTermId = null,
        public ?string $fixedAssetId = null,
    ) {
    }
}
