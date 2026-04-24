<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

use InvalidArgumentException;

/**
 * Human-facing code for a fixed asset (e.g. "MACHINE-001"). Scoped unique
 * per entity by the DB constraint.
 */
final readonly class FixedAssetCode
{
    public function __construct(public string $value)
    {
        if ($value === '' || strlen($value) > 32) {
            throw new InvalidArgumentException('FixedAssetCode must be 1..32 chars.');
        }
        if (!preg_match('/^[A-Za-z0-9\-_\.]+$/', $value)) {
            throw new InvalidArgumentException('FixedAssetCode may only contain [A-Za-z0-9\-_.] characters.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
