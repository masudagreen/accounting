<?php

declare(strict_types=1);

namespace Rucaro\Support\Validation;

use Stringable;

/**
 * Base class for domain value objects.
 *
 * Value objects are identified by their value, not by reference. Subclasses
 * MUST be `final readonly` so they are truly immutable and cheap to compare.
 *
 * Contract:
 * - {@see self::toPrimitive()} returns the canonical scalar (or array) that
 *   the VO wraps; used for persistence and serialisation.
 * - {@see self::equals()} compares by class + primitive value, not object id.
 * - `__toString()` renders the primitive as a string — handy in log lines and
 *   Smarty templates, where scalar coercion is common.
 */
abstract readonly class AbstractValueObject implements Stringable
{
    abstract public function toPrimitive(): mixed;

    /**
     * Two value objects are equal when they are of the same concrete class
     * and their primitive representations are identical.
     */
    public function equals(self $other): bool
    {
        if (static::class !== $other::class) {
            return false;
        }

        return $this->toPrimitive() === $other->toPrimitive();
    }

    public function __toString(): string
    {
        $primitive = $this->toPrimitive();

        if (is_string($primitive)) {
            return $primitive;
        }

        if (is_scalar($primitive) || $primitive instanceof Stringable) {
            return (string) $primitive;
        }

        $encoded = json_encode($primitive);

        return $encoded === false ? '' : $encoded;
    }
}
