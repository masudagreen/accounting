<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

/**
 * Repository port for {@see FixedAsset}.
 */
interface FixedAssetRepositoryInterface
{
    public function save(FixedAsset $asset): void;

    public function findById(string $id): ?FixedAsset;

    public function findByEntityAndCode(string $entityId, string $assetCode): ?FixedAsset;

    /**
     * @return list<FixedAsset>
     */
    public function findByEntity(string $entityId, bool $includeDisposed = false): array;
}
