<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\BreakEvenPoint;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Infrastructure\BreakEvenPoint\PdoAccountTitleCvpClassificationRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoAccountTitleCvpClassificationRepository::class)]
final class PdoCvpClassificationRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;
    private string $entityId = '';
    private string $userId = '';
    private string $accountTitleIdA = '';
    private string $accountTitleIdB = '';

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
            dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'migrate',
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

    public function testBulkUpsertRoundTrips(): void
    {
        $repo = new PdoAccountTitleCvpClassificationRepository($this->requirePdo(), $this->ulids);
        $repo->saveMany([
            new AccountTitleCvpClassification(
                entityId: $this->entityId,
                accountTitleId: $this->accountTitleIdA,
                costType: CvpCostType::Variable,
                variableRatio: '1.0000',
            ),
            new AccountTitleCvpClassification(
                entityId: $this->entityId,
                accountTitleId: $this->accountTitleIdB,
                costType: CvpCostType::Fixed,
                variableRatio: '0.0000',
            ),
        ]);
        $rows = $repo->findAllByEntity($this->entityId);
        self::assertCount(2, $rows);

        // Idempotent upsert — same key, different type.
        $repo->save(new AccountTitleCvpClassification(
            entityId: $this->entityId,
            accountTitleId: $this->accountTitleIdA,
            costType: CvpCostType::SemiVariable,
            variableRatio: '0.5000',
        ));
        $fetched = $repo->findByAccountTitle($this->entityId, $this->accountTitleIdA);
        self::assertNotNull($fetched);
        self::assertSame(CvpCostType::SemiVariable, $fetched->costType);
        self::assertSame('0.5000', $fetched->variableRatio);
    }

    public function testDeleteRemovesRow(): void
    {
        $repo = new PdoAccountTitleCvpClassificationRepository($this->requirePdo(), $this->ulids);
        $repo->save(new AccountTitleCvpClassification(
            entityId: $this->entityId,
            accountTitleId: $this->accountTitleIdA,
            costType: CvpCostType::Fixed,
            variableRatio: '0.0000',
        ));
        $repo->delete($this->entityId, $this->accountTitleIdA);
        self::assertNull($repo->findByAccountTitle($this->entityId, $this->accountTitleIdA));
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
        $this->accountTitleIdA = $this->ulids->generate();
        $this->accountTitleIdB = $this->ulids->generate();

        $pdo->prepare('INSERT INTO users (id, email, display_name, password_hash, is_active, created_at) VALUES (:id, :e, :d, :p, 1, NOW(6))')
            ->execute([
                ':id' => UlidGenerator::decode($this->userId),
                ':e'  => 'test@example.com',
                ':d'  => 'Tester',
                ':p'  => 'x',
            ]);
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at)'
            . ' VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0401\', 1, NOW(6))',
        )->execute([
            ':id'    => UlidGenerator::decode($this->entityId),
            ':owner' => UlidGenerator::decode($this->userId),
            ':n'     => 'Test Entity',
        ]);
        $this->insertAccountTitle($this->accountTitleIdA, '51000', '仕入高', 'expense', 'debit');
        $this->insertAccountTitle($this->accountTitleIdB, '82000', '地代家賃', 'expense', 'debit');
    }

    private function insertAccountTitle(
        string $id,
        string $code,
        string $name,
        string $category,
        string $normalSide,
    ): void {
        $pdo = $this->requirePdo();
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, is_active, sort_order, created_at)'
            . ' VALUES (:id, :e, :c, :n, :cat, :side, 1, 0, NOW(6))',
        )->execute([
            ':id'   => UlidGenerator::decode($id),
            ':e'    => UlidGenerator::decode($this->entityId),
            ':c'    => $code,
            ':n'    => $name,
            ':cat'  => $category,
            ':side' => $normalSide,
        ]);
    }
}
