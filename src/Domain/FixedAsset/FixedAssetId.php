<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

use InvalidArgumentException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * ULID-shaped identifier for a fixed asset.
 */
final readonly class FixedAssetId
{
    public function __construct(public string $value)
    {
        if (!UlidGenerator::isValid($value)) {
            throw new InvalidArgumentException(sprintf('FixedAssetId must be a ULID: %s', $value));
        }
    }

    public function equals(FixedAssetId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
