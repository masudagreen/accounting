<?php

declare(strict_types=1);

namespace Rucaro\Support\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Raised when the caller asks for a service id that has not been registered.
 */
final class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
