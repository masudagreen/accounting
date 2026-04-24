<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

/**
 * Standard fixed-asset category ("区分").
 *
 * Immutable value object backed by the `fixed_asset_categories` table.
 */
final readonly class FixedAssetCategory
{
    public function __construct(
        public string $id,
        public string $code,
        public string $label,
        public ?string $parentCode,
        public int $sortOrder,
        public bool $isTangible,
        public bool $isDepreciable,
        public int $defaultUsefulLifeYears,
        public DepreciationMethod $defaultMethod,
    ) {
    }
}
