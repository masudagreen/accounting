<?php

declare(strict_types=1);

namespace App\Tests\Golden;

use PHPUnit\Framework\TestCase;

/**
 * Golden Master テストの基底クラス.
 *
 * rucaro_golden DB への PDO 接続を管理する.
 * DB が利用できない場合はテスト全体を skip する.
 */
abstract class GoldenMasterTestCase extends TestCase
{
    private static ?\PDO $pdo = null;

    private static bool $connectionChecked = false;

    private static bool $connectionAvailable = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$connectionChecked) {
            self::$connectionChecked = true;
            self::$connectionAvailable = self::tryConnect();
        }

        if (! self::$connectionAvailable) {
            $this->markTestSkipped('Golden DB (rucaro_golden) is not available. Run setup script first.');
        }
    }

    protected static function getGoldenPdo(): \PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::createPdo();
        }
        return self::$pdo;
    }

    private static function tryConnect(): bool
    {
        try {
            self::$pdo = self::createPdo();
            // Verify DB has the expected tables
            $stmt = self::$pdo->query(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'rucaro_golden' AND table_name = 'accountingLog'"
            );
            if ($stmt === false) {
                return false;
            }
            $count = (int) $stmt->fetchColumn();
            return $count > 0;
        } catch (\PDOException) {
            return false;
        }
    }

    private static function createPdo(): \PDO
    {
        $host = getenv('GOLDEN_DB_HOST') ?: '127.0.0.1';
        $port = getenv('GOLDEN_DB_PORT') ?: '3307';
        $dbname = 'rucaro_golden';
        $user = getenv('GOLDEN_DB_USER') ?: 'rucaro';
        $pass = getenv('GOLDEN_DB_PASS') ?: 'rucaro';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbname);
        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    }
}
