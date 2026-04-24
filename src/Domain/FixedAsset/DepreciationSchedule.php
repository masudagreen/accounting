<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

/**
 * Collection of {@see DepreciationScheduleEntry} for a single fixed asset,
 * ordered by period number ascending.
 */
final readonly class DepreciationSchedule
{
    /**
     * @param list<DepreciationScheduleEntry> $entries
     */
    public function __construct(
        public string $fixedAssetId,
        public array $entries,
    ) {
    }
}
