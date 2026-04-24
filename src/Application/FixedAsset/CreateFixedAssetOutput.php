<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\FixedAsset\FixedAsset;

final readonly class CreateFixedAssetOutput
{
    public function __construct(public FixedAsset $asset)
    {
    }
}
