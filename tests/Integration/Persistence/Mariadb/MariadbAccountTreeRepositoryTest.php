<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Mariadb;

use App\Domain\AccountTitle\AccountTree;
use App\Infrastructure\Persistence\Mariadb\MariadbAccountTreeRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * MariadbAccountTreeRepository 統合テスト.
 */
final class MariadbAccountTreeRepositoryTest extends TestCase
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

    public function testLoadCombinedTreeReturnsAccountTree(): void
    {
        $this->assertNotNull($this->pdo);

        $repo = new MariadbAccountTreeRepository($this->pdo);
        $tree = $repo->loadCombinedTree(idEntity: 1, numFiscalPeriod: 1);

        $this->assertInstanceOf(AccountTree::class, $tree);
    }

    public function testLoadCombinedTreeContainsAtLeastOneNode(): void
    {
        $this->assertNotNull($this->pdo);

        $repo  = new MariadbAccountTreeRepository($this->pdo);
        $tree  = $repo->loadCombinedTree(idEntity: 1, numFiscalPeriod: 1);
        $nodes = $tree->walk();

        $this->assertNotEmpty($nodes, 'AccountTree should contain at least one node');
    }
}
