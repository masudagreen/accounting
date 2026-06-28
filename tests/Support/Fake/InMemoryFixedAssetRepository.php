<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;

final class InMemoryFixedAssetRepository implements FixedAssetRepositoryInterface
{
    /** @var array<string, FixedAsset> */
    private array $byId = [];

    #[\Override]
    public function save(FixedAsset $asset): void
    {
        $this->byId[$asset->id] = $asset;
    }

    #[\Override]
    public function findById(string $id): ?FixedAsset
    {
        return $this->byId[$id] ?? null;
    }

    #[\Override]
    public function findByEntityAndCode(string $entityId, string $assetCode): ?FixedAsset
    {
        foreach ($this->byId as $a) {
            if ($a->entityId === $entityId && $a->assetCode === $assetCode) {
                return $a;
            }
        }

        return null;
    }

    #[\Override]
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

        return $out;
    }
}
