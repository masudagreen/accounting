<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Mariadb;

use App\Domain\FixedAssets\FixedAsset;
use App\Infrastructure\Persistence\Mariadb\MariadbFixedAssetRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * MariadbFixedAssetRepository 統合テスト.
 */
final class MariadbFixedAssetRepositoryTest extends TestCase
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

    public function testFindByEntityReturnsListOfFixedAssets(): void
    {
        $this->assertNotNull($this->pdo);

        $repo   = new MariadbFixedAssetRepository($this->pdo);
        $assets = $repo->findByEntity(idEntity: 1);

// assertIsArray removed (already typed as array)
        foreach ($assets as $asset) {
            $this->assertInstanceOf(FixedAsset::class, $asset);
        }
    }

    public function testFindByIdReturnsNullForNonexistentAsset(): void
    {
        $this->assertNotNull($this->pdo);

        $repo  = new MariadbFixedAssetRepository($this->pdo);
        $asset = $repo->findById('nonexistent-id-99999');

        $this->assertNull($asset);
    }

    public function testFindByEntityWithUnknownEntityReturnsEmptyArray(): void
    {
        $this->assertNotNull($this->pdo);

        $repo   = new MariadbFixedAssetRepository($this->pdo);
        $assets = $repo->findByEntity(idEntity: 999999);

        $this->assertSame([], $assets);
    }
}
