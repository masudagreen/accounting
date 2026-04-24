<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\FixedAsset;

final readonly class GetFixedAssetLedgerOutput
{
    /**
     * @param list<array{asset: FixedAsset, schedule: list<DepreciationScheduleEntry>}> $books
     */
    public function __construct(
        public string $entityId,
        public ?string $fiscalTermId,
        public array $books,
        public DateTimeImmutable $generatedAt,
    ) {
    }
}
