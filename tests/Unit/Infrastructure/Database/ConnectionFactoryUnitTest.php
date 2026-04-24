<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Database;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\Database\Exception\DatabaseConnectionException;

/**
 * Unit tests for {@see ConnectionFactory}.
 *
 * These tests never open a real PDO connection; they only exercise the
 * configuration normalisation surface (`configFromArray`, `configFromEnv`)
 * and the DSN that will be handed to PDO. Integration-level tests (which
 * actually connect to a MariaDB) live under tests/Integration.
 */
#[CoversClass(ConnectionFactory::class)]
#[CoversClass(DatabaseConfig::class)]
#[CoversClass(DatabaseConnectionException::class)]
final class ConnectionFactoryUnitTest extends TestCase
{
    // ---------------------------------------------------------------
    // configFromArray / DSN
    // ---------------------------------------------------------------

    public function testConfigFromArrayBuildsExpectedDsn(): void
    {
        $config = ConnectionFactory::configFromArray([
            'host'     => 'db',
            'port'     => 3306,
            'database' => 'rucaro',
            'username' => 'rucaro',
            'password' => 'rucaro',
        ]);

        self::assertSame(
            'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
            $config->toDsn(),
        );
    }

    public function testConfigFromArrayHonoursExplicitCharsetAndPort(): void
    {
        $config = ConnectionFactory::configFromArray([
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'port'      => 13306,
            'database'  => 'rucaro_test',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        self::assertSame(
            'mysql:host=127.0.0.1;port=13306;dbname=rucaro_test;charset=utf8mb4',
            $config->toDsn(),
        );
        self::assertSame('utf8mb4_unicode_ci', $config->collation);
    }

    public function testConfigFromArrayCoercesStringDigitPort(): void
    {
        // config/database.php may read port from env as a string.
        $config = ConnectionFactory::configFromArray([
            'host'     => 'db',
            'port'     => '3307',
            'database' => 'x',
            'username' => 'u',
            'password' => 'p',
        ]);

        self::assertSame(3307, $config->port);
        self::assertStringContainsString('port=3307', $config->toDsn());
    }

    public function testConfigFromArrayRejectsMissingHost(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('host');

        ConnectionFactory::configFromArray([
            'database' => 'x',
            'username' => 'u',
            'password' => 'p',
        ]);
    }

    public function testConfigFromArrayRejectsMissingDatabase(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('database');

        ConnectionFactory::configFromArray([
            'host'     => 'db',
            'username' => 'u',
            'password' => 'p',
        ]);
    }

    public function testConfigFromArrayRejectsNonArrayOptions(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('options');

        ConnectionFactory::configFromArray([
            'host'     => 'db',
            'database' => 'x',
            'username' => 'u',
            'password' => 'p',
            'options'  => 'not-an-array',
        ]);
    }

    public function testConfigFromArrayRejectsNonIntegerOptionKey(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('options keys');

        ConnectionFactory::configFromArray([
            'host'     => 'db',
            'database' => 'x',
            'username' => 'u',
            'password' => 'p',
            'options'  => ['string-key' => 'value'],
        ]);
    }

    public function testConfigFromArrayPropagatesOptions(): void
    {
        $config = ConnectionFactory::configFromArray([
            'host'     => 'db',
            'database' => 'x',
            'username' => 'u',
            'password' => 'p',
            'options'  => [PDO::ATTR_TIMEOUT => 5],
        ]);

        self::assertSame(5, $config->options[PDO::ATTR_TIMEOUT]);
    }

    // ---------------------------------------------------------------
    // configFromEnv
    // ---------------------------------------------------------------

    public function testConfigFromEnvBuildsExpectedDsn(): void
    {
        $config = ConnectionFactory::configFromEnv([
            'DB_HOST'     => 'db',
            'DB_PORT'     => '3306',
            'DB_NAME'     => 'rucaro',
            'DB_USER'     => 'rucaro',
            'DB_PASSWORD' => 'rucaro',
        ]);

        self::assertSame(
            'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
            $config->toDsn(),
        );
        self::assertSame('utf8mb4', $config->charset);
        self::assertSame('utf8mb4_unicode_ci', $config->collation);
    }

    public function testConfigFromEnvPrefersDbPortInternalWhenBothSet(): void
    {
        $config = ConnectionFactory::configFromEnv([
            'DB_HOST'          => 'db',
            'DB_PORT'          => '3306',
            'DB_PORT_INTERNAL' => '13306',
            'DB_NAME'          => 'rucaro',
            'DB_USER'          => 'rucaro',
            'DB_PASSWORD'      => 'rucaro',
        ]);

        self::assertSame(13306, $config->port);
    }

    public function testConfigFromEnvAllowsEmptyPassword(): void
    {
        $config = ConnectionFactory::configFromEnv([
            'DB_HOST'     => 'db',
            'DB_NAME'     => 'x',
            'DB_USER'     => 'root',
            'DB_PASSWORD' => '',
        ]);

        self::assertSame('', $config->password);
    }

    public function testConfigFromEnvDefaultsPortWhenMissing(): void
    {
        $config = ConnectionFactory::configFromEnv([
            'DB_HOST' => 'db',
            'DB_NAME' => 'x',
            'DB_USER' => 'u',
        ]);

        self::assertSame(3306, $config->port);
    }

    public function testConfigFromEnvRejectsMissingHost(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('DB_HOST');

        ConnectionFactory::configFromEnv([
            'DB_NAME' => 'x',
            'DB_USER' => 'u',
        ]);
    }

    public function testConfigFromEnvRejectsMissingName(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('DB_NAME');

        ConnectionFactory::configFromEnv([
            'DB_HOST' => 'db',
            'DB_USER' => 'u',
        ]);
    }

    public function testConfigFromEnvRejectsMissingUser(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('DB_USER');

        ConnectionFactory::configFromEnv([
            'DB_HOST' => 'db',
            'DB_NAME' => 'x',
        ]);
    }

    // ---------------------------------------------------------------
    // Failure path: connection to a nonexistent host.
    //
    // We cannot reliably control OS-level DNS resolution here, so we
    // confine ourselves to a syntactically valid DSN that will fail
    // fast with ECONNREFUSED on most CI runners.
    // ---------------------------------------------------------------

    public function testCreateFromConfigWrapsPdoException(): void
    {
        $config = new DatabaseConfig(
            host: '127.0.0.1',
            dbname: '__nonexistent_db__',
            username: 'no_such_user',
            password: 'no_such_password',
            // 1 is almost never bound on CI runners.
            port: 1,
            options: [PDO::ATTR_TIMEOUT => 1],
        );

        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('[DB]');

        ConnectionFactory::createFromConfig($config);
    }
}
