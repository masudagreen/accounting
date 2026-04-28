<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Migration;

use App\Infrastructure\Migration\MigrationException;
use App\Infrastructure\Migration\MigrationRecordInterface;
use App\Infrastructure\Migration\MigrationRunner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MigrationRunnerTest extends TestCase
{
    private MigrationRecordInterface&MockObject $record;
    private string $migrationsDir;

    protected function setUp(): void
    {
        $this->record = $this->createMock(MigrationRecordInterface::class);
        $this->migrationsDir = sys_get_temp_dir() . '/phpunit_migrations_' . uniqid();
        mkdir($this->migrationsDir);
    }

    protected function tearDown(): void
    {
        // clean up temp migration files
        $sqlFiles = glob($this->migrationsDir . '/*.sql');
        foreach (($sqlFiles !== false ? $sqlFiles : []) as $f) {
            unlink($f);
        }
        if (is_dir($this->migrationsDir)) {
            rmdir($this->migrationsDir);
        }
    }

    // ---- version parsing ----

    public function testParseVersionExtractsNumericPrefix(): void
    {
        $runner = new MigrationRunner($this->record, $this->migrationsDir);

        $version = $runner->parseVersion('0001_initial_schema.up.sql');

        self::assertSame('0001', $version);
    }

    public function testParseVersionReturnsNullForInvalidFilename(): void
    {
        $runner = new MigrationRunner($this->record, $this->migrationsDir);

        $version = $runner->parseVersion('README.md');

        self::assertNull($version);
    }

    public function testParseVersionHandlesLongerVersionNumbers(): void
    {
        $runner = new MigrationRunner($this->record, $this->migrationsDir);

        $version = $runner->parseVersion('0042_add_invoice_columns.down.sql');

        self::assertSame('0042', $version);
    }

    // ---- discovery and sorting ----

    public function testDiscoverUpMigrationsReturnsFilesInAscendingOrder(): void
    {
        file_put_contents($this->migrationsDir . '/0002_something.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0003_more.up.sql', 'SELECT 1;');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $files = $runner->discoverUpMigrations();

        $versions = array_keys($files);
        self::assertSame(['0001', '0002', '0003'], $versions);
    }

    public function testDiscoverDownMigrationsReturnsFilesInDescendingOrder(): void
    {
        file_put_contents($this->migrationsDir . '/0002_something.down.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0001_initial.down.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0003_more.down.sql', 'SELECT 1;');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $files = $runner->discoverDownMigrations();

        $versions = array_keys($files);
        self::assertSame(['0003', '0002', '0001'], $versions);
    }

    public function testDiscoverUpMigrationsIgnoresDownFiles(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0001_initial.down.sql', 'DROP TABLE foo;');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $files = $runner->discoverUpMigrations();

        self::assertCount(1, $files);
        self::assertArrayHasKey('0001', $files);
    }

    public function testDiscoverUpMigrationsIgnoresNonSqlFiles(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/README.md', '# readme');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $files = $runner->discoverUpMigrations();

        self::assertCount(1, $files);
    }

    public function testDiscoverUpMigrationsReturnsEmptyWhenNoFiles(): void
    {
        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $files = $runner->discoverUpMigrations();

        self::assertSame([], $files);
    }

    // ---- pending migrations ----

    public function testGetPendingReturnsOnlyUnappliedMigrations(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0002_second.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0003_third.up.sql', 'SELECT 1;');

        $this->record
            ->expects(self::once())
            ->method('getAppliedVersions')
            ->willReturn(['0001', '0002']);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $pending = $runner->getPending();

        self::assertSame(['0003'], array_keys($pending));
    }

    public function testGetPendingReturnsAllWhenNoneApplied(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0002_second.up.sql', 'SELECT 1;');

        $this->record
            ->expects(self::once())
            ->method('getAppliedVersions')
            ->willReturn([]);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $pending = $runner->getPending();

        self::assertCount(2, $pending);
    }

    public function testGetPendingReturnsEmptyWhenAllApplied(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');

        $this->record
            ->method('getAppliedVersions')
            ->willReturn(['0001']);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $pending = $runner->getPending();

        self::assertSame([], $pending);
    }

    // ---- target filtering ----

    public function testGetPendingUpToTargetFiltersVersionsAboveTarget(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0002_second.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0003_third.up.sql', 'SELECT 1;');

        $this->record
            ->method('getAppliedVersions')
            ->willReturn([]);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $pending = $runner->getPendingUpTo('0002');

        self::assertSame(['0001', '0002'], array_keys($pending));
    }

    public function testGetAppliedDownToTargetFiltersVersionsBelowTarget(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.down.sql', 'DROP TABLE t1;');
        file_put_contents($this->migrationsDir . '/0002_second.down.sql', 'DROP TABLE t2;');
        file_put_contents($this->migrationsDir . '/0003_third.down.sql', 'DROP TABLE t3;');

        $this->record
            ->method('getAppliedVersions')
            ->willReturn(['0001', '0002', '0003']);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $toRollback = $runner->getAppliedDownTo('0002');

        // rollback 0003 only (down to but NOT including 0002 itself)
        self::assertSame(['0003'], array_keys($toRollback));
    }

    // ---- status report ----

    public function testGetStatusReturnsAppliedAndPendingInfo(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0002_second.up.sql', 'SELECT 1;');

        $this->record
            ->method('getAppliedVersions')
            ->willReturn(['0001']);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $status = $runner->getStatus();

        self::assertCount(2, $status);
        self::assertSame('applied', $status['0001']);
        self::assertSame('pending', $status['0002']);
    }

    // ---- exception on missing down file ----

    public function testGetAppliedDownToThrowsWhenDownFileIsMissing(): void
    {
        // only up file exists, no down file
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');

        $this->record
            ->method('getAppliedVersions')
            ->willReturn(['0001']);

        $runner = new MigrationRunner($this->record, $this->migrationsDir);

        $this->expectException(MigrationException::class);
        $runner->getAppliedDownTo('0000');
    }

    // ---- buildNewFilename ----

    public function testBuildNewFilenameFormatsCorrectly(): void
    {
        file_put_contents($this->migrationsDir . '/0001_initial.up.sql', 'SELECT 1;');
        file_put_contents($this->migrationsDir . '/0002_second.up.sql', 'SELECT 1;');

        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $name = $runner->buildNewFilename('add_invoice_columns');

        self::assertSame('0003_add_invoice_columns', $name);
    }

    public function testBuildNewFilenameStartsAtZeroZeroZeroOneWhenNoMigrations(): void
    {
        $runner = new MigrationRunner($this->record, $this->migrationsDir);
        $name = $runner->buildNewFilename('initial_schema');

        self::assertSame('0001_initial_schema', $name);
    }
}
