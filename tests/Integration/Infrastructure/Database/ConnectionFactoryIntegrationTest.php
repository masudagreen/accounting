<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\Database;

use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\Database\Exception\DatabaseConnectionException;

/**
 * Integration test for {@see ConnectionFactory}.
 *
 * Requires env:
 *   RUCARO_TEST_DB_HOST      (default "127.0.0.1")
 *   RUCARO_TEST_DB_PORT      (default 3306)
 *   RUCARO_TEST_DB_NAME      (default "rucaro_test")
 *   RUCARO_TEST_DB_USER      (required to run)
 *   RUCARO_TEST_DB_PASSWORD  (default "")
 *
 * The test creates/drops the target database itself, so the user must be
 * authorised for CREATE DATABASE on the host. If env is not set the test
 * is skipped so `phpunit --testsuite=Unit` stays green on bare laptops.
 */
#[CoversClass(ConnectionFactory::class)]
#[CoversClass(DatabaseConfig::class)]
final class ConnectionFactoryIntegrationTest extends TestCase
{
    private string $host = '';
    private int $port = 3306;
    private string $dbname = '';
    private string $username = '';
    private string $password = '';

    protected function setUp(): void
    {
        $user = getenv('RUCARO_TEST_DB_USER');
        if ($user === false || $user === '') {
            $this->markTestSkipped('RUCARO_TEST_DB_USER is not set; skipping DB integration test.');
        }

        $this->username = (string) $user;
        $this->host     = ((string) getenv('RUCARO_TEST_DB_HOST')) ?: '127.0.0.1';
        $portEnv        = getenv('RUCARO_TEST_DB_PORT');
        $this->port     = $portEnv !== false && $portEnv !== '' ? (int) $portEnv : 3306;
        $this->dbname   = ((string) getenv('RUCARO_TEST_DB_NAME')) ?: 'rucaro_test';
        $pwEnv          = getenv('RUCARO_TEST_DB_PASSWORD');
        $this->password = $pwEnv === false ? '' : (string) $pwEnv;

        $root = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        $root->exec(sprintf(
            'CREATE DATABASE `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->dbname,
        ));
    }

    protected function tearDown(): void
    {
        if ($this->dbname === '' || $this->username === '') {
            return;
        }
        try {
            $root = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );
            $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        } catch (PDOException) {
            // best-effort cleanup
        }
    }

    public function testCreateFromConfigReturnsUsablePdoAndRunsSelectOne(): void
    {
        $pdo = ConnectionFactory::createFromConfig($this->config());

        $stmt = $pdo->query('SELECT 1 AS one');
        self::assertNotFalse($stmt);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        self::assertIsArray($row);
        self::assertArrayHasKey('one', $row);
        // With STRINGIFY_FETCHES=false on PHP 8.1+, MySQL integers come back as int.
        self::assertSame(1, $row['one']);
    }

    public function testConnectionUsesUtf8mb4AndUtcSessionContract(): void
    {
        $pdo = ConnectionFactory::createFromConfig($this->config());

        $csStmt = $pdo->query('SELECT @@character_set_connection AS cs');
        self::assertNotFalse($csStmt);
        self::assertSame('utf8mb4', $csStmt->fetchColumn());

        $tzStmt = $pdo->query('SELECT @@session.time_zone AS tz');
        self::assertNotFalse($tzStmt);
        self::assertSame('+00:00', $tzStmt->fetchColumn());
    }

    public function testErrmodeExceptionIsActive(): void
    {
        $pdo = ConnectionFactory::createFromConfig($this->config());

        $this->expectException(PDOException::class);
        $pdo->query('SELECT FROM');
    }

    public function testCreateFromArraySucceeds(): void
    {
        $pdo = ConnectionFactory::createFromArray([
            'host'     => $this->host,
            'port'     => $this->port,
            'database' => $this->dbname,
            'username' => $this->username,
            'password' => $this->password,
        ]);

        $stmt = $pdo->query('SELECT 1');
        self::assertNotFalse($stmt);
        self::assertSame(1, (int) $stmt->fetchColumn());
    }

    public function testCreateFromEnvSucceeds(): void
    {
        $pdo = ConnectionFactory::createFromEnv([
            'DB_HOST'     => $this->host,
            'DB_PORT'     => (string) $this->port,
            'DB_NAME'     => $this->dbname,
            'DB_USER'     => $this->username,
            'DB_PASSWORD' => $this->password,
        ]);

        $stmt = $pdo->query('SELECT 1');
        self::assertNotFalse($stmt);
        self::assertSame(1, (int) $stmt->fetchColumn());
    }

    public function testUtf8mb4RoundTripPreservesEmojiAndKanji(): void
    {
        $pdo = ConnectionFactory::createFromConfig($this->config());
        $pdo->exec(
            'CREATE TABLE t_utf8 ('
            . ' id INT NOT NULL PRIMARY KEY,'
            . ' value VARCHAR(64) NOT NULL'
            . ') ENGINE=InnoDB'
            . ' DEFAULT CHARACTER SET utf8mb4'
            . ' COLLATE utf8mb4_unicode_ci'
        );

        $input = '日本語テスト 🐙🦀';
        $ins = $pdo->prepare('INSERT INTO t_utf8 (id, value) VALUES (1, :v)');
        $ins->execute([':v' => $input]);

        $sel = $pdo->query('SELECT value FROM t_utf8 WHERE id = 1');
        self::assertNotFalse($sel);
        self::assertSame($input, $sel->fetchColumn());
    }

    public function testBadCredentialsRaiseDatabaseConnectionException(): void
    {
        $bad = new DatabaseConfig(
            host: $this->host,
            dbname: $this->dbname,
            username: 'no_such_user_' . bin2hex(random_bytes(3)),
            password: 'no_such_password',
            port: $this->port,
        );

        $this->expectException(DatabaseConnectionException::class);
        $this->expectExceptionMessage('[DB]');

        ConnectionFactory::createFromConfig($bad);
    }

    private function config(): DatabaseConfig
    {
        return new DatabaseConfig(
            host: $this->host,
            dbname: $this->dbname,
            username: $this->username,
            password: $this->password,
            port: $this->port,
        );
    }
}
