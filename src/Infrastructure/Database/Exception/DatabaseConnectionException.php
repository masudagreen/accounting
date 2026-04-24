<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Database\Exception;

use RuntimeException;
use Throwable;

/**
 * Raised when a PDO connection cannot be established or the database
 * configuration is invalid.
 *
 * Every message is prefixed with "[DB] " so grepping operational logs is
 * trivial and so we never leak raw PDO message formats (which on some
 * drivers include the connection string and credentials) out to callers
 * without a clear marker that this failure originated in the DB layer.
 */
final class DatabaseConnectionException extends RuntimeException
{
    private const MESSAGE_PREFIX = '[DB] ';

    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        if (!str_starts_with($message, self::MESSAGE_PREFIX)) {
            $message = self::MESSAGE_PREFIX . $message;
        }
        parent::__construct($message, $code, $previous);
    }

    public static function fromPdoFailure(string $detail, Throwable $previous): self
    {
        return new self(
            sprintf('connection failed: %s', $detail),
            previous: $previous,
        );
    }

    public static function invalidConfig(string $reason): self
    {
        return new self(sprintf('invalid configuration: %s', $reason));
    }

    public static function missingEnv(string $key): self
    {
        return new self(sprintf('required env var missing: %s', $key));
    }
}
