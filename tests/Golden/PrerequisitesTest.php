<?php

declare(strict_types=1);

namespace App\Tests\Golden;

/**
 * Golden Master 前提条件テスト.
 *
 * rucaro_golden DB に接続でき、主要テーブルが揃っていることを確認する.
 * DB が利用できない環境 (CI 等) では全テストが skip される.
 */
final class PrerequisitesTest extends GoldenMasterTestCase
{
    /** 期待するテーブル一覧. */
    private const array REQUIRED_TABLES = [
        'accountingEntity',
        'accountingLog',
        'accountingFSValueJpn',
        'accountingFSJpn',
        'accountingLogCalcJpn',
    ];

    public function testCanConnectToGoldenDb(): void
    {
        $pdo = self::getGoldenPdo();

        $stmt = $pdo->query("SELECT 1 AS ping");
        self::assertNotFalse($stmt);
        $row = $stmt->fetch();
        self::assertIsArray($row);
        self::assertSame('1', (string) ($row['ping'] ?? ''));
    }

    public function testRequiredTablesExist(): void
    {
        $pdo = self::getGoldenPdo();

        $placeholders = implode(',', array_fill(0, count(self::REQUIRED_TABLES), '?'));
        $stmt = $pdo->prepare(
            "SELECT table_name FROM information_schema.tables
             WHERE table_schema = 'rucaro_golden' AND table_name IN ({$placeholders})"
        );
        $stmt->execute(self::REQUIRED_TABLES);

        /** @var list<string> $found */
        $found = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach (self::REQUIRED_TABLES as $table) {
            self::assertContains(
                $table,
                $found,
                sprintf('Required table %s is missing from rucaro_golden', $table)
            );
        }
    }

    public function testAtLeastOneEntityIsRegistered(): void
    {
        $pdo = self::getGoldenPdo();

        $stmt = $pdo->query("SELECT COUNT(*) FROM accountingEntity");
        self::assertNotFalse($stmt);
        $count = (int) $stmt->fetchColumn();

        self::assertGreaterThanOrEqual(1, $count, 'accountingEntity must have at least 1 row');

        // Report count only (no individual data)
        self::addToAssertionCount(0); // no-op to satisfy coverage
        fwrite(STDERR, sprintf("\n[Golden] accountingEntity rows: %d\n", $count));
    }

    public function testTableRowCounts(): void
    {
        $pdo = self::getGoldenPdo();

        $tables = [
            'accountingLog',
            'accountingLogCalcJpn',
            'accountingFSValueJpn',
            'accountingFSJpn',
            'accountingEntity',
        ];

        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            self::assertNotFalse($stmt, "Failed to query {$table}");
            $count = (int) $stmt->fetchColumn();
            fwrite(STDERR, sprintf("[Golden] %s: %d rows\n", $table, $count));
            self::assertGreaterThanOrEqual(0, $count);
        }
    }

    public function testAccountingLogHasFlagRemoveColumn(): void
    {
        $pdo = self::getGoldenPdo();

        $stmt = $pdo->query(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = 'rucaro_golden'
               AND table_name = 'accountingLog'
               AND column_name = 'flagRemove'"
        );
        self::assertNotFalse($stmt);
        $count = (int) $stmt->fetchColumn();

        self::assertSame(1, $count, 'accountingLog must have flagRemove column');
    }

    public function testAccountingLogHasJsonVersionColumn(): void
    {
        $pdo = self::getGoldenPdo();

        $stmt = $pdo->query(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = 'rucaro_golden'
               AND table_name = 'accountingLog'
               AND column_name = 'jsonVersion'"
        );
        self::assertNotFalse($stmt);
        $count = (int) $stmt->fetchColumn();

        self::assertSame(1, $count, 'accountingLog must have jsonVersion column');
    }

    public function testFlagRemoveDistribution(): void
    {
        $pdo = self::getGoldenPdo();

        $stmt = $pdo->query(
            "SELECT flagRemove, COUNT(*) as cnt FROM accountingLog GROUP BY flagRemove"
        );
        self::assertNotFalse($stmt);

        $rows = $stmt->fetchAll();
        self::assertIsArray($rows);

        $total = 0;
        $active = 0;
        foreach ($rows as $row) {
            $cnt = (int) ($row['cnt'] ?? 0);
            $total += $cnt;
            if ((int) ($row['flagRemove'] ?? 0) === 0) {
                $active = $cnt;
            }
        }

        fwrite(STDERR, sprintf("[Golden] accountingLog total: %d, active (flagRemove=0): %d\n", $total, $active));
        self::assertGreaterThan(0, $active, 'Must have at least one active journal entry');
    }
}
