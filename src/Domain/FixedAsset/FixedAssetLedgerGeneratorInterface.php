<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

use Rucaro\Application\FixedAsset\GetFixedAssetLedgerOutput;

interface FixedAssetLedgerGeneratorInterface
{
    public function render(GetFixedAssetLedgerOutput $ledger): string;
}
