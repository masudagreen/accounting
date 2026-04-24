<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Logging;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rucaro\Infrastructure\Logging\Exception\LoggerConfigurationException;
use Rucaro\Infrastructure\Logging\LoggerFactory;

#[CoversClass(LoggerFactory::class)]
#[CoversClass(LoggerConfigurationException::class)]
final class LoggerFactoryTest extends TestCase
{
    /**
     * @return array{
     *     default: string,
     *     channels: array<string, array{handlers: list<array<string, mixed>>}>,
     * }
     */
    private function fixtureConfig(): array
    {
        return [
            'default' => 'app',
            'channels' => [
                'app' => [
                    'handlers' => [
                        ['type' => 'stream', 'path' => 'php://memory', 'level' => 'debug'],
                        ['type' => 'errorlog', 'level' => 'warning'],
                    ],
                ],
                'testing' => [
                    'handlers' => [
                        ['type' => 'stream', 'path' => 'php://memory', 'level' => 'debug'],
                    ],
                ],
                'audit' => [
                    'handlers' => [
                        [
                            'type' => 'rotating_file',
                            'path' => 'storage/logs/audit.log',
                            'level' => 'info',
                            'max_files' => 30,
                        ],
                    ],
                ],
            ],
        ];
    }

    #[Test]
    public function createReturnsPsrAndMonologLoggerForKnownChannel(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $logger = $factory->create('app');

        self::assertInstanceOf(LoggerInterface::class, $logger);
        self::assertInstanceOf(Logger::class, $logger);
        self::assertSame('app', $logger->getName());
    }

    #[Test]
    public function createWithoutArgumentUsesDefaultChannel(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $logger = $factory->create();

        self::assertInstanceOf(Logger::class, $logger);
        self::assertSame('app', $logger->getName());
    }

    #[Test]
    public function unknownChannelThrowsLoggerConfigurationException(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $this->expectException(LoggerConfigurationException::class);
        $this->expectExceptionMessage('channel not defined');

        $factory->create('does-not-exist');
    }

    #[Test]
    public function missingChannelsKeyThrows(): void
    {
        $this->expectException(LoggerConfigurationException::class);

        new LoggerFactory(['default' => 'app']);
    }

    #[Test]
    public function createRegistersAllHandlersDefinedInConfig(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $logger = $factory->create('app');

        self::assertInstanceOf(Logger::class, $logger);
        $handlers = $logger->getHandlers();
        self::assertCount(2, $handlers);
        self::assertInstanceOf(StreamHandler::class, $handlers[0]);
        self::assertInstanceOf(ErrorLogHandler::class, $handlers[1]);
        self::assertSame(Level::Debug, $handlers[0]->getLevel());
        self::assertSame(Level::Warning, $handlers[1]->getLevel());
    }

    #[Test]
    public function rotatingFileHandlerIsInstantiatedForRotatingFileType(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $logger = $factory->create('audit');
        self::assertInstanceOf(Logger::class, $logger);

        $handlers = $logger->getHandlers();
        self::assertCount(1, $handlers);
        self::assertInstanceOf(RotatingFileHandler::class, $handlers[0]);
        self::assertSame(Level::Info, $handlers[0]->getLevel());
    }

    #[Test]
    public function processorsIncludePsrLogMessageAndUidProcessor(): void
    {
        $factory = new LoggerFactory($this->fixtureConfig());

        $logger = $factory->create('testing');
        self::assertInstanceOf(Logger::class, $logger);
        $processors = $logger->getProcessors();

        $hasPsr = false;
        $hasUid = false;
        foreach ($processors as $processor) {
            if ($processor instanceof PsrLogMessageProcessor) {
                $hasPsr = true;
            }
            if ($processor instanceof UidProcessor) {
                $hasUid = true;
            }
        }
        self::assertTrue($hasPsr, 'PsrLogMessageProcessor must be registered');
        self::assertTrue($hasUid, 'UidProcessor must be registered');
    }

    #[Test]
    public function psrPlaceholdersAreInterpolatedInOutput(): void
    {
        $stream = fopen('php://memory', 'w+');
        self::assertIsResource($stream);

        $config = [
            'default' => 'cap',
            'channels' => [
                'cap' => [
                    'handlers' => [
                        ['type' => 'stream', 'path' => $stream, 'level' => 'debug'],
                    ],
                ],
            ],
        ];

        $factory = new LoggerFactory($config);
        $logger = $factory->create('cap');

        $logger->info('Hello {name}', ['name' => 'Rucaro']);

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertIsString($contents);
        self::assertStringContainsString('Hello Rucaro', $contents);
    }

    #[Test]
    public function uidProcessorAttachesStableUuidPerLoggerInstance(): void
    {
        $stream = fopen('php://memory', 'w+');
        self::assertIsResource($stream);

        $config = [
            'default' => 'cap',
            'channels' => [
                'cap' => [
                    'handlers' => [
                        ['type' => 'stream', 'path' => $stream, 'level' => 'debug'],
                    ],
                ],
            ],
        ];

        $factory = new LoggerFactory($config);
        $logger = $factory->create('cap');

        $logger->info('first');
        $logger->info('second');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertIsString($contents);

        // Each record must contain a "uid" extra in Monolog's default LineFormatter output.
        $matches = [];
        $found = preg_match_all('/"uid":"([a-f0-9]+)"/', $contents, $matches);
        self::assertSame(2, $found, 'both records should carry a uid');
        self::assertSame($matches[1][0], $matches[1][1], 'uid must be stable across records from the same logger');
    }

    #[Test]
    public function unknownHandlerTypeThrows(): void
    {
        $factory = new LoggerFactory([
            'default' => 'broken',
            'channels' => [
                'broken' => [
                    'handlers' => [
                        ['type' => 'mystery-handler', 'level' => 'debug'],
                    ],
                ],
            ],
        ]);

        $this->expectException(LoggerConfigurationException::class);
        $this->expectExceptionMessage('unknown handler type');

        $factory->create('broken');
    }

    #[Test]
    public function invalidLogLevelThrows(): void
    {
        $factory = new LoggerFactory([
            'default' => 'broken',
            'channels' => [
                'broken' => [
                    'handlers' => [
                        ['type' => 'stream', 'path' => 'php://memory', 'level' => 'not-a-level'],
                    ],
                ],
            ],
        ]);

        $this->expectException(LoggerConfigurationException::class);
        $this->expectExceptionMessage('invalid log level');

        $factory->create('broken');
    }

    #[Test]
    public function rotatingFileRequiresPath(): void
    {
        $factory = new LoggerFactory([
            'default' => 'broken',
            'channels' => [
                'broken' => [
                    'handlers' => [
                        ['type' => 'rotating_file', 'level' => 'debug'],
                    ],
                ],
            ],
        ]);

        $this->expectException(LoggerConfigurationException::class);
        $this->expectExceptionMessage('path');

        $factory->create('broken');
    }
}
