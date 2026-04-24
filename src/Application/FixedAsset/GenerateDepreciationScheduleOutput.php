<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;

final readonly class GenerateDepreciationScheduleOutput
{
    /**
     * @param list<DepreciationScheduleEntry> $entries
     */
    public function __construct(public array $entries)
    {
    }
}
