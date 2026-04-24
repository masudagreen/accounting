<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Exception;

use Rucaro\Domain\Exception\DomainException;

/**
 * Minimal concrete subclass used exclusively to exercise the abstract
 * DomainException base class from unit tests.
 */
final class ConcreteDomainException extends DomainException
{
}
