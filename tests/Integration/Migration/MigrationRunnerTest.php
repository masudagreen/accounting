<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Migration;

use PDO;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Migration\MigrationRunner;

/**
 * Integration test for MigrationRunner.
 *
 * Requires env:
 *   RUCARO_TEST_DB_DSN   e.g. "mysql:host=127.0.0.1;port=3306"
 *   RUCARO_TEST_DB_USER  e.g. "root"
 *   RUCARO_TEST_DB_PASS  e.g. "secret"
 *   RUCARO_TEST_DB_NAME  optional, defaults to "rucaro_test"
 *
 * If these are not set, the test is skipped.
 */
final class MigrationRunnerTest extends TestCase
{
    /** @var list<string> 10 tables expected after a clean up(). */
    private const EXPECTED_TABLES = [
        'users',
        'api_tokens',
        'entities',
        'fiscal_terms',
        'account_titles',
        'sub_account_titles',
        'journal_entries',
        'journal_entry_lines',
        'receipts',
        'receipt_action_logs',
        'approval_tokens',
    ];

    private ?PDO $pdo = null;
    private string $dbName = '';

    protected function setUp(): void
    {
        $dsn  = getenv('RUCARO_TEST_DB_DSN');
        $user = getenv('RUCARO_TEST_DB_USER');
        $pass = getenv('RUCARO_TEST_DB_PASS');
        $name = getenv('RUCARO_TEST_DB_NAME') ?: 'rucaro_test';

        if ($dsn === false || $user === false) {
            $this->markTestSkipped('RUCARO_TEST_DB_* env vars are not set; skipping DB integration test.');
        }

        $root = new PDO($dsn, $user, $pass === false ? '' : $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $root->exec("DROP DATABASE IF EXISTS `$name`");
        $root->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->dbName = $name;
        $this->pdo = new PDO(
            $dsn . ';dbname=' . $name,
            $user,
            $pass === false ? '' : $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
        $this->pdo->exec("SET NAMES utf8mb4");
        $this->pdo->exec("SET time_zone = '+00:00'");
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null && $this->dbName !== '') {
            $this->pdo->exec("DROP DATABASE IF EXISTS `$this->dbName`");
        }
    }

    public function testUpCreatesAllExpectedTables(): void
    {
        $runner = new MigrationRunner($this->pdo, $this->migrationsDir());

        $applied = $runner->up();
        $this->assertGreaterThanOrEqual(4, $applied, 'At least 4 migration files should be applied');

        $tables = $this->listTables();
        foreach (self::EXPECTED_TABLES as $expected) {
            $this->assertContains($expected, $tables, "Table $expected should exist after up()");
        }
        $this->assertContains('schema_migrations', $tables);
    }

    public function testDownRollsBackLastMigration(): void
    {
        $runner = new MigrationRunner($this->pdo, $this->migrationsDir());
        $runner->up();

        $tablesBefore = $this->listTables();
        $this->assertContains('approval_tokens', $tablesBefore);
        $this->assertContains('receipts', $tablesBefore);

        $rolledBack = $runner->down(1);
        $this->assertSame(1, $rolledBack);

        $tablesAfter = $this->listTables();
        $this->assertNotContains('approval_tokens', $tablesAfter, 'approval_tokens should be dropped by down()');
        $this->assertNotContains('receipts', $tablesAfter, 'receipts should be dropped by down()');
        $this->assertContains('journal_entries', $tablesAfter, 'journal_entries should still exist');
    }

    public function testStatusReflectsAppliedAndPending(): void
    {
        $runner = new MigrationRunner($this->pdo, $this->migrationsDir());
        $before = $runner->status();
        foreach ($before as $row) {
            if ($row['version'] === '0000') {
                continue;
            }
            $this->assertFalse($row['applied'], "Migration {$row['version']} must start pending");
        }

        $runner->up();
        $after = $runner->status();
        foreach ($after as $row) {
            $this->assertTrue($row['applied'], "Migration {$row['version']} must be applied after up()");
        }
    }

    /** @return list<string> */
    private function listTables(): array
    {
        $stmt = $this->pdo->query('SHOW TABLES');
        $rows = $stmt->fetchAll(PDO::FETCH_NUM);
        return array_map(static fn (array $r): string => (string) $r[0], $rows);
    }

    private function migrationsDir(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR
            . 'scripts' . DIRECTORY_SEPARATOR . 'migrate';
    }
}
