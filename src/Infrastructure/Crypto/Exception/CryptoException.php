<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto\Exception;

use RuntimeException;

/**
 * Raised when any cipher operation fails: tampering detected, wrong AAD,
 * malformed payload, unsupported version, legacy decryption failure, or
 * invalid master key material.
 *
 * Extends RuntimeException so callers may catch it uniformly as an
 * infrastructure-level runtime error.
 */
final class CryptoException extends RuntimeException
{
}
