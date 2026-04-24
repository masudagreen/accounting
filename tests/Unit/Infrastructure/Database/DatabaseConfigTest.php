<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Database;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\Database\Exception\DatabaseConnectionException;

#[CoversClass(DatabaseConfig::class)]
#[CoversClass(DatabaseConnectionException::class)]
final class DatabaseConfigTest extends TestCase
{
    public function testHoldsAllRequiredValuesAndDefaults(): void
    {
        $config = new DatabaseConfig(
            host: 'db',
            dbname: 'rucaro',
            username: 'rucaro',
            password: 'secret',
        );

        self::assertSame('db', $config->host);
        self::assertSame('rucaro', $config->dbname);
        self::assertSame('rucaro', $config->username);
        self::assertSame('secret', $config->password);
        self::assertSame(3306, $config->port);
        self::assertSame('utf8mb4', $config->charset);
        self::assertSame('utf8mb4_unicode_ci', $config->collation);
        self::assertSame('mysql', $config->driver);
        self::assertSame([], $config->options);
    }

    public function testDefaultPdoOptionsEnforceSecureContract(): void
    {
        $options = DatabaseConfig::defaultPdoOptions();

        self::assertSame(PDO::ERRMODE_EXCEPTION, $options[PDO::ATTR_ERRMODE]);
        self::assertSame(PDO::FETCH_ASSOC, $options[PDO::ATTR_DEFAULT_FETCH_MODE]);
        self::assertFalse($options[PDO::ATTR_EMULATE_PREPARES]);
        self::assertFalse($options[PDO::ATTR_STRINGIFY_FETCHES]);
    }

    public function testEffectiveOptionsMergesDefaultsWithOverrides(): void
    {
        $config = new DatabaseConfig(
            host: 'db',
            dbname: 'rucaro',
            username: 'u',
            password: 'p',
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::ATTR_PERSISTENT => true,
            ],
        );

        $effective = $config->effectiveOptions();

        // Override wins for ERRMODE.
        self::assertSame(PDO::ERRMODE_WARNING, $effective[PDO::ATTR_ERRMODE]);
        // Added key survives.
        self::assertTrue($effective[PDO::ATTR_PERSISTENT]);
        // Defaults that were not overridden still present.
        self::assertSame(PDO::FETCH_ASSOC, $effective[PDO::ATTR_DEFAULT_FETCH_MODE]);
        self::assertFalse($effective[PDO::ATTR_EMULATE_PREPARES]);
    }

    public function testToDsnFormatIsStable(): void
    {
        $config = new DatabaseConfig(
            host: '127.0.0.1',
            dbname: 'my_app',
            username: 'u',
            password: 'p',
            port: 3307,
        );

        self::assertSame(
            'mysql:host=127.0.0.1;port=3307;dbname=my_app;charset=utf8mb4',
            $config->toDsn(),
        );
    }

    public function testToDsnHonoursCustomCharsetAndDriver(): void
    {
        $config = new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: 'p',
            charset: 'utf8mb3',
            driver: 'mariadb',
        );

        self::assertStringStartsWith('mariadb:', $config->toDsn());
        self::assertStringContainsString('charset=utf8mb3', $config->toDsn());
    }

    public function testWithOptionsReturnsNewInstanceWithMergedOptions(): void
    {
        $config = new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: 'p',
            options: [PDO::ATTR_PERSISTENT => false],
        );

        $copy = $config->withOptions([PDO::ATTR_TIMEOUT => 5]);

        self::assertNotSame($config, $copy);
        self::assertArrayNotHasKey(PDO::ATTR_TIMEOUT, $config->options);
        self::assertSame(5, $copy->options[PDO::ATTR_TIMEOUT]);
        self::assertFalse($copy->options[PDO::ATTR_PERSISTENT]);
    }

    public function testEmptyHostRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('[DB]');
        $this->expectExceptionMessage('host');

        new DatabaseConfig(host: '', dbname: 'x', username: 'u', password: 'p');
    }

    public function testEmptyDbnameRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('dbname');

        new DatabaseConfig(host: 'db', dbname: '', username: 'u', password: 'p');
    }

    public function testEmptyUsernameRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('username');

        new DatabaseConfig(host: 'db', dbname: 'x', username: '', password: 'p');
    }

    public function testPortOutOfRangeRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('port');

        new DatabaseConfig(host: 'db', dbname: 'x', username: 'u', password: 'p', port: 0);
    }

    public function testPortAbove65535Rejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);

        new DatabaseConfig(host: 'db', dbname: 'x', username: 'u', password: 'p', port: 70000);
    }

    public function testEmptyCharsetRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('charset');

        new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: 'p',
            charset: '',
        );
    }

    public function testEmptyCollationRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('collation');

        new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: 'p',
            collation: '',
        );
    }

    public function testEmptyDriverRejected(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('driver');

        new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: 'p',
            driver: '',
        );
    }

    public function testEmptyPasswordIsAllowed(): void
    {
        // Some MariaDB test containers ship a rootless passwordless account.
        $config = new DatabaseConfig(
            host: 'db',
            dbname: 'x',
            username: 'u',
            password: '',
        );

        self::assertSame('', $config->password);
    }
}
