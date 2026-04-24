<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Logging;

use InvalidArgumentException;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Rucaro\Infrastructure\Logging\Exception\LoggerConfigurationException;
use Throwable;
use ValueError;

/**
 * Builds {@see \Monolog\Logger} instances from the channel/handler map
 * defined in `config/logging.php`.
 *
 * Responsibilities:
 *   - Resolve the configured channel to a concrete list of handlers.
 *   - Attach the Phase 2 baseline processors
 *     ({@see PsrLogMessageProcessor}, {@see UidProcessor}) so every log
 *     entry carries a correlation id and supports `{placeholder}` expansion.
 *   - Normalise formatters (`line` or `json`) across handlers.
 *
 * The factory is deliberately not a singleton: callers decide whether to
 * cache the returned logger, and tests instantiate a fresh factory per case.
 */
final readonly class LoggerFactory
{
    /** @var array<string, array{handlers: list<array<string, mixed>>}> */
    private array $channels;

    private string $defaultChannel;

    private string $projectRoot;

    /**
     * @param array<string, mixed>|null $config
     *        Normally the return value of `config/logging.php`. Pass `null`
     *        to load that file directly.
     * @param string|null $projectRoot
     *        Base directory used to resolve relative file paths. Defaults to
     *        the repository root (two levels above this file).
     */
    public function __construct(
        ?array $config = null,
        ?string $projectRoot = null,
    ) {
        $resolved = $config ?? self::loadDefaultConfig();

        if (!isset($resolved['channels']) || !is_array($resolved['channels'])) {
            throw LoggerConfigurationException::missingChannels();
        }

        /** @var array<string, array{handlers: list<array<string, mixed>>}> $channels */
        $channels = $resolved['channels'];
        $this->channels = $channels;

        $this->defaultChannel = is_string($resolved['default'] ?? null)
            ? $resolved['default']
            : 'app';

        $this->projectRoot = $projectRoot ?? dirname(__DIR__, 3);
    }

    /**
     * Build a logger for the requested channel, or the configured default
     * when no channel name is supplied.
     */
    public function create(?string $channel = null): LoggerInterface
    {
        $name = $channel ?? $this->defaultChannel;

        if (!array_key_exists($name, $this->channels)) {
            throw LoggerConfigurationException::unknownChannel($name);
        }

        $definition = $this->channels[$name];
        $handlerSpecs = $definition['handlers'];
        if ($handlerSpecs === []) {
            throw LoggerConfigurationException::missingHandlerField($name, 'handlers');
        }

        $handlers = [];
        foreach ($handlerSpecs as $spec) {
            if (!is_array($spec)) {
                throw LoggerConfigurationException::missingHandlerField($name, 'handlers[]');
            }
            $handlers[] = $this->buildHandler($spec);
        }

        $processors = [
            new PsrLogMessageProcessor(),
            new UidProcessor(),
        ];

        return new Logger($name, $handlers, $processors);
    }

    /**
     * @param array<string, mixed> $spec
     */
    private function buildHandler(array $spec): HandlerInterface
    {
        $type = is_string($spec['type'] ?? null) ? $spec['type'] : '';
        $level = $this->resolveLevel($spec['level'] ?? 'debug');

        $handler = match ($type) {
            'stream' => $this->buildStreamHandler($spec, $level),
            'rotating_file' => $this->buildRotatingFileHandler($spec, $level),
            'stderr' => new StreamHandler('php://stderr', $level),
            'errorlog' => new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level),
            default => throw LoggerConfigurationException::unknownHandlerType($type),
        };

        $handler->setFormatter($this->resolveFormatter($spec));

        return $handler;
    }

    /**
     * @param array<string, mixed> $spec
     */
    private function buildStreamHandler(array $spec, Level $level): StreamHandler
    {
        /** @var mixed $path */
        $path = $spec['path'] ?? null;
        if (!is_string($path) && !is_resource($path)) {
            throw LoggerConfigurationException::missingHandlerField('stream', 'path');
        }

        try {
            return new StreamHandler($path, $level);
        } catch (InvalidArgumentException $e) {
            throw new LoggerConfigurationException(
                'failed to open stream: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @param array<string, mixed> $spec
     */
    private function buildRotatingFileHandler(array $spec, Level $level): RotatingFileHandler
    {
        $rawPath = $spec['path'] ?? null;
        if (!is_string($rawPath) || $rawPath === '') {
            throw LoggerConfigurationException::missingHandlerField('rotating_file', 'path');
        }

        $path = $this->resolvePath($rawPath);
        $maxFiles = is_int($spec['max_files'] ?? null) ? (int) $spec['max_files'] : 0;

        try {
            return new RotatingFileHandler($path, $maxFiles, $level);
        } catch (Throwable $e) {
            throw new LoggerConfigurationException(
                'failed to initialise rotating_file handler: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @param array<string, mixed> $spec
     */
    private function resolveFormatter(array $spec): LineFormatter|JsonFormatter
    {
        $formatter = is_string($spec['formatter'] ?? null) ? $spec['formatter'] : 'line';

        return match ($formatter) {
            'json' => new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES, appendNewline: true),
            'line' => new LineFormatter(
                format: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                dateFormat: 'Y-m-d\TH:i:s.uP',
                allowInlineLineBreaks: true,
                ignoreEmptyContextAndExtra: true,
            ),
            default => throw LoggerConfigurationException::unknownHandlerType(
                'formatter:' . $formatter,
            ),
        };
    }

    private function resolveLevel(mixed $level): Level
    {
        if ($level instanceof Level) {
            return $level;
        }

        if (is_int($level)) {
            try {
                return Level::from($level);
            } catch (ValueError $e) {
                throw LoggerConfigurationException::invalidLevel((string) $level);
            }
        }

        if (is_string($level) && $level !== '') {
            try {
                return Level::fromName(strtoupper($level));
            } catch (Throwable $e) {
                throw LoggerConfigurationException::invalidLevel($level);
            }
        }

        throw LoggerConfigurationException::invalidLevel((string) (is_scalar($level) ? $level : 'non-scalar'));
    }

    private function resolvePath(string $path): string
    {
        if (
            str_contains($path, '://')
            || $this->isAbsolutePath($path)
        ) {
            return $path;
        }

        return rtrim($this->projectRoot, "/\\") . DIRECTORY_SEPARATOR . ltrim($path, "/\\");
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        // POSIX absolute
        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }

        // Windows drive-letter absolute (e.g. C:\...)
        return (bool) preg_match('/^[A-Za-z]:[\\\\\/]/', $path);
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadDefaultConfig(): array
    {
        $path = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'logging.php';
        if (!is_file($path)) {
            throw new LoggerConfigurationException(
                'default config file not found: ' . $path,
            );
        }

        /** @var mixed $loaded */
        $loaded = require $path;
        if (!is_array($loaded)) {
            throw new LoggerConfigurationException(
                'config/logging.php must return an array',
            );
        }

        /** @var array<string, mixed> $loaded */
        return $loaded;
    }
}
