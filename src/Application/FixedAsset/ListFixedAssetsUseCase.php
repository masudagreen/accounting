<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;

final readonly class ListFixedAssetsUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
    ) {
    }

    /**
     * @return list<FixedAsset>
     */
    public function execute(string $entityId, bool $includeDisposed = false): array
    {
        return $this->assets->findByEntity($entityId, $includeDisposed);
    }
}
