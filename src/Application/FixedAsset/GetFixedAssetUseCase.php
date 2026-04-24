<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;

final readonly class GetFixedAssetUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
    ) {
    }

    public function execute(string $id): ?FixedAsset
    {
        return $this->assets->findById($id);
    }
}
