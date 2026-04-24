<?php

declare(strict_types=1);

namespace Rucaro\Support\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Raised when service resolution fails for reasons other than the id being
 * unknown (e.g. the factory threw).
 */
class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
