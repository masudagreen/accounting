<?php

declare(strict_types=1);

namespace Rucaro\Domain\Common\ValueObject;

use Rucaro\Support\Validation\AbstractValueObject;
use Rucaro\Support\Validation\Assert;

/**
 * Reference implementation of a domain value object.
 *
 * Keeps a canonical email string and validates it at construction. Later
 * phases will add normalization (lowercase, IDN) once the surrounding
 * domain settles.
 */
final readonly class EmailAddress extends AbstractValueObject
{
    public function __construct(private string $value)
    {
        Assert::email($this->value, 'email');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
