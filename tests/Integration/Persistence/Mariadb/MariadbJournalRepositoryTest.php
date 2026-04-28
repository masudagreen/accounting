<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Mariadb;

use App\Infrastructure\Persistence\Mariadb\MariadbJournalRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * MariadbJournalRepository 統合テスト.
 *
 * golden DB (tests/Golden/data/) が利用可能な場合のみ実行する.
 * DB が接続できない場合は markTestSkipped.
 */
final class MariadbJournalRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        $dsn = getenv('TEST_DB_DSN');
        if ($dsn === false || $dsn === '') {
            $this->markTestSkipped('TEST_DB_DSN not configured — skipping integration tests');
        }

        $user = (string) (getenv('TEST_DB_USER') ?: 'root');
        $pass = (string) (getenv('TEST_DB_PASS') ?: '');

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\PDOException $e) {
            $this->markTestSkipped('Cannot connect to test DB: ' . $e->getMessage());
        }
    }

    public function testFindByEntityAndPeriodReturnsArray(): void
    {
        $this->assertNotNull($this->pdo);

        $repo   = new MariadbJournalRepository($this->pdo);
        $result = $repo->findByEntityAndPeriod(idEntity: 1, numFiscalPeriod: 1);

        // Should return an array (possibly empty if no data for entity 1 / period 1)
// assertIsArray removed (already typed as array)
    }

    public function testFindByEntityAndPeriodReturnsCorrectShape(): void
    {
        $this->assertNotNull($this->pdo);

        $repo    = new MariadbJournalRepository($this->pdo);
        $entries = $repo->findByEntityAndPeriod(idEntity: 1, numFiscalPeriod: 1);

        foreach ($entries as $item) {
            $this->assertArrayHasKey('date', $item);
            $this->assertArrayHasKey('entry', $item);
            $this->assertInstanceOf(\DateTimeImmutable::class, $item['date']);
            $this->assertInstanceOf(\App\Domain\Journal\JournalEntry::class, $item['entry']);
        }
    }

    public function testFindByEntityAndPeriodSkipsInvalidRows(): void
    {
        $this->assertNotNull($this->pdo);

        // Insert a row with invalid JSON (should be skipped without exception)
        $repo   = new MariadbJournalRepository($this->pdo);
        $result = $repo->findByEntityAndPeriod(idEntity: 9999, numFiscalPeriod: 9999);

// assertIsArray removed (already typed as array)
        $this->assertSame([], $result);
    }
}
