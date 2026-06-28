<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Migration;

use PDO;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Migration\Migration;
use Rucaro\Infrastructure\Migration\MigrationRunner;

/**
 * Unit test for MigrationRunner::discover() and seed file handling.
 *
 * Uses a temp directory of fake .sql files; no DB required.
 *
 * Regression: until v4 the discover() regex matched both `0008_x.sql`
 * and `0008_x_seed.sql` under the same version key, so the seed entry
 * silently overwrote the schema entry and `up()` tried to INSERT into
 * a table that did not yet exist.
 */
final class MigrationRunnerDiscoverTest extends TestCase
{
    private string $tmpDir = '';

    #[\Override]
    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'rucaro-mig-'.bin2hex(random_bytes(6));
        if (!mkdir($this->tmpDir, 0700, true)) {
            $this->fail('failed to create tmp dir');
        }
    }

    #[\Override]
    protected function tearDown(): void
    {
        if ($this->tmpDir === '' || !is_dir($this->tmpDir)) {
            return;
        }
        foreach (glob($this->tmpDir.\DIRECTORY_SEPARATOR.'*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($this->tmpDir);
    }

    public function testSchemaAndSeedAtSameVersionAreBothPreserved(): void
    {
        $this->writeFile('0001_schema.sql', "CREATE TABLE foo (id INT);\n");
        $this->writeFile('0001_schema_seed.sql', "INSERT INTO foo VALUES (1);\n");
        $this->writeFile('0002_other.sql', "CREATE TABLE bar (id INT);\n");

        $migrations = $this->newRunner()->discover();

        $this->assertCount(2, $migrations, 'should yield exactly 2 migrations (one per version)');
        $byVersion = $this->indexByVersion($migrations);

        $this->assertArrayHasKey('0001', $byVersion);
        $this->assertSame('schema', $byVersion['0001']->name);
        $this->assertTrue(
            $byVersion['0001']->hasSeed(),
            'version 0001 must remember its companion *_seed.sql file',
        );
        $this->assertStringContainsString(
            'CREATE TABLE foo',
            $byVersion['0001']->readUpSql(),
            'up SQL must be the schema file (not overwritten by seed)',
        );
        $this->assertStringContainsString(
            'INSERT INTO foo',
            $byVersion['0001']->readSeedSql(),
            'seed SQL must be the seed file',
        );
    }

    public function testSeedWithoutSchemaIsIgnored(): void
    {
        $this->writeFile('0001_orphan_seed.sql', "INSERT INTO nowhere VALUES (1);\n");
        $this->writeFile('0002_real.sql', "CREATE TABLE r (id INT);\n");

        $migrations = $this->newRunner()->discover();

        $this->assertCount(1, $migrations, 'orphan seed without matching schema should be skipped');
        $this->assertSame('0002', $migrations[0]->version);
    }

    public function testDownFileIsNotMistakenForUp(): void
    {
        $this->writeFile('0001_x.sql', "CREATE TABLE x (id INT);\n");
        $this->writeFile('0001_x.down.sql', "DROP TABLE x;\n");

        $migrations = $this->newRunner()->discover();

        $this->assertCount(1, $migrations);
        $this->assertTrue($migrations[0]->hasDown());
        $this->assertFalse($migrations[0]->hasSeed());
    }

    public function testApplyOrderRunsSchemaBeforeSeed(): void
    {
        $this->writeFile('0001_schema.sql', "CREATE TABLE foo (id INT);\n");
        $this->writeFile('0001_schema_seed.sql', "INSERT INTO foo VALUES (1);\n");

        $migration = $this->newRunner()->discover()[0];
        $combined = $migration->readUpSql()."\n".$migration->readSeedSql();

        // schema CREATE must come strictly before INSERT.
        $createPos = strpos($combined, 'CREATE TABLE foo');
        $insertPos = strpos($combined, 'INSERT INTO foo');
        $this->assertNotFalse($createPos);
        $this->assertNotFalse($insertPos);
        $this->assertLessThan($insertPos, $createPos, 'CREATE must precede INSERT in apply payload');
    }

    private function writeFile(string $name, string $body): void
    {
        $path = $this->tmpDir.\DIRECTORY_SEPARATOR.$name;
        if (file_put_contents($path, $body) === false) {
            $this->fail("failed to write fixture file: $name");
        }
    }

    private function newRunner(): MigrationRunner
    {
        // discover() does not touch the PDO; pass an in-memory SQLite stub
        // only because the constructor demands a non-null PDO.
        $pdo = new \PDO('sqlite::memory:');

        return new MigrationRunner($pdo, $this->tmpDir);
    }

    /**
     * @param list<Migration> $migrations
     *
     * @return array<string, Migration>
     */
    private function indexByVersion(array $migrations): array
    {
        $out = [];
        foreach ($migrations as $m) {
            $out[$m->version] = $m;
        }

        return $out;
    }
}
