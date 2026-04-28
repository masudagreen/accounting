<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * 固定資産1件の減価償却計算結果を UI に渡すための DTO.
 */
final readonly class DepreciationDto
{
    public function __construct(
        public readonly string $assetId,
        public readonly string $assetName,
        public readonly int $depreciation,
        public readonly int $accumulatedClosing,
        public readonly int $bookValueClosing,
        public readonly int $monthsUsedInPeriod,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'assetId'            => $this->assetId,
            'assetName'          => $this->assetName,
            'depreciation'       => $this->depreciation,
            'accumulatedClosing' => $this->accumulatedClosing,
            'bookValueClosing'   => $this->bookValueClosing,
            'monthsUsedInPeriod' => $this->monthsUsedInPeriod,
        ];
    }
}
