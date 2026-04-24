<?php

declare(strict_types=1);

namespace Rucaro\Application\BreakEvenPoint;

/**
 * DTO for one row of a CVP classification bulk upsert.
 */
final readonly class UpsertCvpClassificationInput
{
    public function __construct(
        public string $accountTitleId,
        public string $costType,
        public string $variableRatio = '1.0000',
        public ?string $notes = null,
    ) {
    }
}
