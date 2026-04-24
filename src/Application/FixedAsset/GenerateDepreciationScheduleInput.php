<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;

/**
 * Input for {@see GenerateDepreciationScheduleUseCase}. A single fiscal term
 * window + its ULID drives the computation; the use case builds entries for
 * every asset (if `fixedAssetId` is null) or one asset (otherwise).
 */
final readonly class GenerateDepreciationScheduleInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fiscalTermStart,
        public DateTimeImmutable $fiscalTermEnd,
        public ?string $fixedAssetId = null,
    ) {
    }
}
