<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

interface FixedAssetCategoryRepositoryInterface
{
    /**
     * @return list<FixedAssetCategory>
     */
    public function findAll(): array;

    public function findByCode(string $code): ?FixedAssetCategory;
}
