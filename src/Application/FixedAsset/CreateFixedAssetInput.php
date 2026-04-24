<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;

final readonly class CreateFixedAssetInput
{
    public function __construct(
        public string $entityId,
        public string $assetCode,
        public string $assetName,
        public string $categoryCode,
        public ?string $assetAccountTitleId,
        public ?string $accumulatedDepreciationAccountTitleId,
        public ?string $depreciationExpenseAccountTitleId,
        public DateTimeImmutable $acquisitionDate,
        public DateTimeImmutable $serviceStartDate,
        public string $acquisitionCost,
        public string $residualValue,
        public int $usefulLifeYears,
        public string $method,
        public int $quantity,
        public ?string $departmentCode,
        public ?string $note,
        public string $createdBy,
    ) {
    }
}
