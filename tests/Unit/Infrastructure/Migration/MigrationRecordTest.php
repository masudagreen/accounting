<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Migration;

use App\Infrastructure\Migration\MigrationRecord;
use PHPUnit\Framework\TestCase;

final class MigrationRecordTest extends TestCase
{
    public function testGetTableNameReturnsExpectedDefault(): void
    {
        // MigrationRecord holds the canonical table name
        // We verify this without a real DB by checking the constant
        self::assertSame('schema_migrations', MigrationRecord::TABLE_NAME);
    }

    public function testCreateTableSqlContainsVersionColumn(): void
    {
        $sql = MigrationRecord::createTableSql();

        self::assertStringContainsString('version', $sql);
        self::assertStringContainsString(MigrationRecord::TABLE_NAME, $sql);
    }

    public function testCreateTableSqlContainsAppliedAtColumn(): void
    {
        $sql = MigrationRecord::createTableSql();

        self::assertStringContainsString('applied_at', $sql);
    }

    public function testCreateTableSqlContainsPrimaryKey(): void
    {
        $sql = MigrationRecord::createTableSql();

        self::assertStringContainsStringIgnoringCase('PRIMARY KEY', $sql);
    }
}
