<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Logging\Exception;

use RuntimeException;
use Throwable;

/**
 * Raised when the logging configuration cannot be resolved into a concrete
 * {@see \Monolog\Logger}.
 *
 * Every message is prefixed with "[LOG] " so operational grep stays trivial
 * and so logger-layer failures never masquerade as generic runtime errors.
 */
final class LoggerConfigurationException extends RuntimeException
{
    private const MESSAGE_PREFIX = '[LOG] ';

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

    public static function missingChannels(): self
    {
        return new self('configuration must define a "channels" array');
    }

    public static function unknownChannel(string $channel): self
    {
        return new self(sprintf('channel not defined: %s', $channel));
    }

    public static function unknownHandlerType(string $type): self
    {
        return new self(sprintf('unknown handler type: %s', $type));
    }

    public static function invalidLevel(string $level): self
    {
        return new self(sprintf('invalid log level: %s', $level));
    }

    public static function missingHandlerField(string $type, string $field): self
    {
        return new self(sprintf('%s handler requires field: %s', $type, $field));
    }
}
