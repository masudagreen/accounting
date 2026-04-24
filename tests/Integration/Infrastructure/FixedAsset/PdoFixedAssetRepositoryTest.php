<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\FixedAsset;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Infrastructure\FixedAsset\PdoFixedAssetRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Requires the same RUCARO_TEST_DB_* env vars as the ledger test; skips
 * cleanly when unset.
 */
#[CoversClass(PdoFixedAssetRepository::class)]
final class PdoFixedAssetRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;
    private string $entityId = '';
    private string $userId = '';

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
        $this->pdo->exec('SET NAMES utf8mb4');
        $this->pdo->exec("SET time_zone = '+00:00'");
        $runner = new MigrationRunner(
            $this->pdo,
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'migrate',
        );
        $runner->up();
        $this->ulids = new UlidGenerator();
        $this->seed();
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null && $this->dbName !== '') {
            $this->pdo->exec("DROP DATABASE IF EXISTS `$this->dbName`");
        }
    }

    public function testSaveRoundTrip(): void
    {
        $repo = new PdoFixedAssetRepository($this->requirePdo());
        $asset = $this->makeAsset('M-100');
        $repo->save($asset);
        $found = $repo->findByEntityAndCode($this->entityId, 'M-100');
        self::assertNotNull($found);
        self::assertSame('M-100', $found->assetCode);
        self::assertSame('1000000.0000', $found->acquisitionCost);
        self::assertSame(DepreciationMethod::StraightLine, $found->method);
    }

    public function testFindByEntityListsOnlyLive(): void
    {
        $repo = new PdoFixedAssetRepository($this->requirePdo());
        $repo->save($this->makeAsset('M-101'));
        $repo->save($this->makeAsset('M-102'));
        $list = $repo->findByEntity($this->entityId);
        self::assertCount(2, $list);
    }

    private function requirePdo(): PDO
    {
        if ($this->pdo === null) {
            $this->fail('PDO not initialised.');
        }
        return $this->pdo;
    }

    private function seed(): void
    {
        $pdo = $this->requirePdo();
        $this->userId = $this->ulids->generate();
        $this->entityId = $this->ulids->generate();
        $pdo->prepare('INSERT INTO users (id, email, display_name, password_hash, is_active, created_at) VALUES (:id, :e, :d, :p, 1, NOW(6))')
            ->execute([
                ':id' => UlidGenerator::decode($this->userId),
                ':e'  => 'test@example.com',
                ':d'  => 'Tester',
                ':p'  => 'x',
            ]);
        $pdo->prepare('INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at) VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0401\', 1, NOW(6))')
            ->execute([
                ':id'    => UlidGenerator::decode($this->entityId),
                ':owner' => UlidGenerator::decode($this->userId),
                ':n'     => 'Test Entity',
            ]);
    }

    private function makeAsset(string $code): FixedAsset
    {
        return new FixedAsset(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            assetCode: $code,
            assetName: 'Asset ' . $code,
            categoryCode: 'machinery',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: new DateTimeImmutable('2025-04-01'),
            serviceStartDate: new DateTimeImmutable('2025-04-01'),
            disposalDate: null,
            acquisitionCost: '1000000.0000',
            residualValue: '0.0000',
            usefulLifeYears: 10,
            method: DepreciationMethod::StraightLine,
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: $this->userId,
            createdAt: new DateTimeImmutable('2025-04-01'),
            updatedAt: new DateTimeImmutable('2025-04-01'),
            deletedAt: null,
        );
    }
}
