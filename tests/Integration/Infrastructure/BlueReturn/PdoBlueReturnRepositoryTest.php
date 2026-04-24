<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\BlueReturn;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Infrastructure\BlueReturn\PdoBlueReturnRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoBlueReturnRepository::class)]
final class PdoBlueReturnRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;
    private string $entityId = '';
    private string $fiscalTermId = '';
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

    public function testRoundTripsSnapshotPayload(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBlueReturnRepository($pdo);

        $snapshot = new BlueReturnSnapshot(
            page1Pl: ['formType' => 'general', 'netIncome' => '1234000'],
            page2Monthly: ['months' => [['month' => 1, 'sales' => '800000']]],
            page3Breakdown: ['depreciation' => [['name' => '車両', 'amount' => '120000']]],
            page4Bs: ['assets' => [['label' => '現金', 'amount' => '600000']]],
        );
        $form = $this->buildDraft($snapshot);
        $repo->save($form);

        $loaded = $repo->findById($form->id);
        self::assertNotNull($loaded);
        self::assertSame('1234000', $loaded->snapshot->page1Pl['netIncome']);
        self::assertSame(BlueReturnStatus::Draft, $loaded->status);
    }

    public function testFinalizePersists(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBlueReturnRepository($pdo);
        $form = $this->buildDraft();
        $repo->save($form);

        $finalized = $form->finalize(new DateTimeImmutable('2026-03-15T00:00:00Z'));
        $repo->save($finalized);

        $loaded = $repo->findById($form->id);
        self::assertNotNull($loaded);
        self::assertSame(BlueReturnStatus::Finalized, $loaded->status);
        self::assertNotNull($loaded->finalizedAt);
    }

    public function testDeleteIsSoftDelete(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBlueReturnRepository($pdo);
        $form = $this->buildDraft();
        $repo->save($form);

        $repo->delete($form->id);
        self::assertNull($repo->findById($form->id));
        self::assertSame([], $repo->findByEntity($this->entityId));
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
        $this->fiscalTermId = $this->ulids->generate();
        $pdo->prepare('INSERT INTO users (id, email, display_name, password_hash, is_active, created_at) VALUES (:id, :e, :d, :p, 1, NOW(6))')
            ->execute([
                ':id' => UlidGenerator::decode($this->userId),
                ':e'  => 'test@example.com',
                ':d'  => 'Tester',
                ':p'  => 'x',
            ]);
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_corporate, is_active, created_at)'
            . ' VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0101\', 0, 1, NOW(6))',
        )->execute([
            ':id'    => UlidGenerator::decode($this->entityId),
            ':owner' => UlidGenerator::decode($this->userId),
            ':n'     => 'Sole Proprietor',
        ]);
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at)'
            . ' VALUES (:id, :e, 1, \'2026-01-01\', \'2026-12-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
    }

    private function buildDraft(?BlueReturnSnapshot $snapshot = null): BlueReturnForm
    {
        $now = new DateTimeImmutable('2026-01-10T00:00:00Z');
        return new BlueReturnForm(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            formType: BlueReturnFormType::General,
            status: BlueReturnStatus::Draft,
            snapshot: $snapshot ?? BlueReturnSnapshot::empty(BlueReturnFormType::General),
            finalizedAt: null,
            createdBy: $this->userId,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
