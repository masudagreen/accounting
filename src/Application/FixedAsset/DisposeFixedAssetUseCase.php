<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;

final readonly class DisposeFixedAssetUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
    ) {
    }

    public function execute(string $id, DateTimeImmutable $disposalDate): FixedAsset
    {
        $asset = $this->assets->findById($id);
        if ($asset === null) {
            throw EntityNotFoundException::for('fixed_asset', $id);
        }
        $disposed = $asset->dispose($disposalDate);
        $this->assets->save($disposed);
        return $disposed;
    }
}
