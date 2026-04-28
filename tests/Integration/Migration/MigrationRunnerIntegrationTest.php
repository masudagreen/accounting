<?php

declare(strict_types=1);

namespace App\Tests\Integration\Migration;

use App\Infrastructure\Migration\MigrationRecord;
use App\Infrastructure\Migration\MigrationRunner;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Integration test using SQLite in-memory.
 * No MariaDB required — safe for CI environments.
 */
final class MigrationRunnerIntegrationTest extends TestCase
{
    private PDO $pdo;
    private MigrationRecord $record;
    private string $migrationsDir;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->record = new MigrationRecord($this->pdo);
        $this->record->ensureTable();

        $this->migrationsDir = sys_get_temp_dir() . '/phpunit_migration_int_' . uniqid();
        mkdir($this->migrationsDir);
    }

    protected function tearDown(): void
    {
        $sqlFiles = glob($this->migrationsDir . '/*.sql');
        foreach (($sqlFiles !== false ? $sqlFiles : []) as $f) {
            unlink($f);
        }
        if (is_dir($this->migrationsDir)) {
            rmdir($this->migrationsDir);
        }
    }

    public function testEnsureTableCreatesSchemaMigrationsTable(): void
    {
        $stmt = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='schema_migrations'"
        );
        self::assertNotFalse($stmt);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        self::assertNotFalse($row);
        self::assertIsArray($row);
        self::assertSame('schema_migrations', $row['name']);
    }

    public function testGetAppliedVersionsReturnsEmptyInitially(): void
    {
        $versions = $this->record->getAppliedVersions();

        self::assertSame([], $versions);
    }

    public function testMarkAppliedRecordsVersion(): void
    {
        $this->record->markApplied('0001');

        $versions = $this->record->getAppliedVersions();

        self::assertContains('0001', $versions);
    }

    public function testMarkRolledBackRemovesVersion(): void
    {
        $this->record->markApplied('0001');
        $this->record->markRolledBack('0001');

        $versions = $this->record->getAppliedVersions();

        self::assertNotContains('0001', $versions);
    }

    public function testIsAppliedReturnsTrueAfterMarking(): void
    {
        $this->record->markApplied('0002');

        self::assertTrue($this->record->isApplied('0002'));
    }

    public function testIsAppliedReturnsFalseForUnknownVersion(): void
    {
        self::assertFalse($this->record->isApplied('9999'));
    }

    public function testRunUpExecutesSqlAndMarksApplied(): void
    {
        file_put_contents(
            $this->migrationsDir . '/0001_create_fruit.up.sql',
            'CREATE TABLE fruit (id INTEGER PRIMARY KEY, name TEXT NOT NULL);'
        );
        file_put_contents(
            $this->migrationsDir . '/0001_create_fruit.down.sql',
            'DROP TABLE IF EXISTS fruit;'
        );

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $runner->runUp($this->pdo);

        // table should exist
        $stmt = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='fruit'"
        );
        self::assertNotFalse($stmt);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        self::assertNotFalse($row);
        self::assertIsArray($row);
        self::assertSame('fruit', $row['name']);

        // version marked
        self::assertTrue($this->record->isApplied('0001'));
    }

    public function testRunUpIsIdempotentWhenAlreadyApplied(): void
    {
        file_put_contents(
            $this->migrationsDir . '/0001_create_fruit.up.sql',
            'CREATE TABLE fruit (id INTEGER PRIMARY KEY, name TEXT NOT NULL);'
        );

        $this->record->markApplied('0001');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        // Should not throw even though table was never actually created
        $runner->runUp($this->pdo);

        self::assertTrue($this->record->isApplied('0001'));
    }

    public function testRunDownDropsTableAndRemovesRecord(): void
    {
        // Create the table first via up
        $this->pdo->exec(
            'CREATE TABLE vegetable (id INTEGER PRIMARY KEY, name TEXT NOT NULL);'
        );
        $this->record->markApplied('0001');

        file_put_contents(
            $this->migrationsDir . '/0001_create_vegetable.up.sql',
            'CREATE TABLE vegetable (id INTEGER PRIMARY KEY, name TEXT NOT NULL);'
        );
        file_put_contents(
            $this->migrationsDir . '/0001_create_vegetable.down.sql',
            'DROP TABLE IF EXISTS vegetable;'
        );

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $runner->runDown($this->pdo);

        $stmt = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='vegetable'"
        );
        self::assertNotFalse($stmt);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        self::assertFalse($row);

        self::assertFalse($this->record->isApplied('0001'));
    }

    public function testRunUpWithTargetStopsAtSpecifiedVersion(): void
    {
        file_put_contents(
            $this->migrationsDir . '/0001_create_a.up.sql',
            'CREATE TABLE table_a (id INTEGER PRIMARY KEY);'
        );
        file_put_contents(
            $this->migrationsDir . '/0002_create_b.up.sql',
            'CREATE TABLE table_b (id INTEGER PRIMARY KEY);'
        );
        file_put_contents(
            $this->migrationsDir . '/0003_create_c.up.sql',
            'CREATE TABLE table_c (id INTEGER PRIMARY KEY);'
        );

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $runner->runUpTo($this->pdo, '0002');

        self::assertTrue($this->record->isApplied('0001'));
        self::assertTrue($this->record->isApplied('0002'));
        self::assertFalse($this->record->isApplied('0003'));
    }

    public function testRunDownWithTargetRollsBackToSpecifiedVersion(): void
    {
        // Apply 3 migrations manually
        $this->pdo->exec('CREATE TABLE table_a (id INTEGER PRIMARY KEY);');
        $this->pdo->exec('CREATE TABLE table_b (id INTEGER PRIMARY KEY);');
        $this->pdo->exec('CREATE TABLE table_c (id INTEGER PRIMARY KEY);');
        $this->record->markApplied('0001');
        $this->record->markApplied('0002');
        $this->record->markApplied('0003');

        file_put_contents($this->migrationsDir . '/0001_create_a.up.sql', 'CREATE TABLE table_a (id INTEGER PRIMARY KEY);');
        file_put_contents($this->migrationsDir . '/0001_create_a.down.sql', 'DROP TABLE IF EXISTS table_a;');
        file_put_contents($this->migrationsDir . '/0002_create_b.up.sql', 'CREATE TABLE table_b (id INTEGER PRIMARY KEY);');
        file_put_contents($this->migrationsDir . '/0002_create_b.down.sql', 'DROP TABLE IF EXISTS table_b;');
        file_put_contents($this->migrationsDir . '/0003_create_c.up.sql', 'CREATE TABLE table_c (id INTEGER PRIMARY KEY);');
        file_put_contents($this->migrationsDir . '/0003_create_c.down.sql', 'DROP TABLE IF EXISTS table_c;');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        // rollback down to 0002 means 0003 is reverted, 0001 and 0002 remain
        $runner->runDownTo($this->pdo, '0002');

        self::assertTrue($this->record->isApplied('0001'));
        self::assertTrue($this->record->isApplied('0002'));
        self::assertFalse($this->record->isApplied('0003'));
    }

    public function testMultipleSqlStatementsInOneMigrationFileAllExecute(): void
    {
        $sql = implode("\n", [
            'CREATE TABLE multi_a (id INTEGER PRIMARY KEY);',
            'CREATE TABLE multi_b (id INTEGER PRIMARY KEY);',
        ]);
        file_put_contents($this->migrationsDir . '/0001_multi.up.sql', $sql);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $runner->runUp($this->pdo);

        foreach (['multi_a', 'multi_b'] as $tbl) {
            $stmt = $this->pdo->query(
                "SELECT name FROM sqlite_master WHERE type='table' AND name='$tbl'"
            );
            self::assertNotFalse($stmt);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            self::assertNotFalse($row, "Table $tbl should exist");
            self::assertIsArray($row);
        }
    }
}
