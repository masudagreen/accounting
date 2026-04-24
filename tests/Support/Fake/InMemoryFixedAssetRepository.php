<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;

final class InMemoryFixedAssetRepository implements FixedAssetRepositoryInterface
{
    /** @var array<string, FixedAsset> */
    private array $byId = [];

    public function save(FixedAsset $asset): void
    {
        $this->byId[$asset->id] = $asset;
    }

    public function findById(string $id): ?FixedAsset
    {
        return $this->byId[$id] ?? null;
    }

    public function findByEntityAndCode(string $entityId, string $assetCode): ?FixedAsset
    {
        foreach ($this->byId as $a) {
            if ($a->entityId === $entityId && $a->assetCode === $assetCode) {
                return $a;
            }
        }
        return null;
    }

    public function findByEntity(string $entityId, bool $includeDisposed = false): array
    {
        $out = [];
        foreach ($this->byId as $a) {
            if ($a->entityId !== $entityId || $a->deletedAt !== null) {
                continue;
            }
            if (!$includeDisposed && $a->disposalDate !== null) {
                continue;
            }
            $out[] = $a;
        }
        return array_values($out);
    }
}
