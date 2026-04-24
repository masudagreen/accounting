<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\CashPlan;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Infrastructure\CashPlan\PdoCashPlanRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoCashPlanRepository::class)]
final class PdoCashPlanRepositoryTest extends TestCase
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

    public function testSaveRoundTripWithEntries(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoCashPlanRepository($pdo);
        $plan = $this->buildPlan([
            $this->entry(CashPlanCategory::OperatingIn, '売上入金', [
                '100000.0000', '100000.0000', '100000.0000', '100000.0000', '100000.0000', '100000.0000',
                '100000.0000', '100000.0000', '100000.0000', '100000.0000', '100000.0000', '100000.0000',
            ]),
            $this->entry(CashPlanCategory::OperatingOut, '給与', [
                '30000.0000', '30000.0000', '30000.0000', '30000.0000', '30000.0000', '30000.0000',
                '30000.0000', '30000.0000', '30000.0000', '30000.0000', '30000.0000', '30000.0000',
            ]),
        ]);
        $repo->save($plan);

        $found = $repo->findById($plan->id);
        self::assertNotNull($found);
        self::assertSame('Plan A', $found->name);
        self::assertCount(2, $found->entries);
        self::assertSame('70000.0000', $found->monthlyDelta(1));

        $byEntity = $repo->findByEntity($this->entityId);
        self::assertCount(1, $byEntity);
    }

    public function testDeleteIsSoftDelete(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoCashPlanRepository($pdo);
        $plan = $this->buildPlan([]);
        $repo->save($plan);

        $repo->delete($plan->id);
        self::assertNull($repo->findById($plan->id));
        self::assertSame([], $repo->findByEntity($this->entityId));
    }

    public function testSaveReplacesEntriesAtomically(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoCashPlanRepository($pdo);
        $repo->save($this->buildPlan([
            $this->entry(CashPlanCategory::OperatingIn, 'orig', array_fill(0, 12, '100.0000')),
        ]));
        $loaded = $repo->findByEntityAndName($this->entityId, $this->fiscalTermId, 'Plan A');
        self::assertNotNull($loaded);

        $replacement = new CashPlan(
            id: $loaded->id,
            entityId: $loaded->entityId,
            fiscalTermId: $loaded->fiscalTermId,
            name: $loaded->name,
            openingBalance: $loaded->openingBalance,
            currencyCode: $loaded->currencyCode,
            notes: 'changed',
            entries: [
                $this->entry(CashPlanCategory::FinancingIn, 'replacement', array_fill(0, 12, '200.0000')),
            ],
            createdBy: $loaded->createdBy,
            createdAt: $loaded->createdAt,
            updatedAt: new DateTimeImmutable('2026-05-01T00:00:00Z'),
        );
        $repo->save($replacement);

        $after = $repo->findById($loaded->id);
        self::assertNotNull($after);
        self::assertSame('changed', $after->notes);
        self::assertCount(1, $after->entries);
        self::assertSame('replacement', $after->entries[0]->label);
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
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at)'
            . ' VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0401\', 1, NOW(6))',
        )->execute([
            ':id'    => UlidGenerator::decode($this->entityId),
            ':owner' => UlidGenerator::decode($this->userId),
            ':n'     => 'Test Entity',
        ]);
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at)'
            . ' VALUES (:id, :e, 1, \'2025-04-01\', \'2026-03-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
    }

    /**
     * @param list<CashPlanEntry> $entries
     */
    private function buildPlan(array $entries): CashPlan
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new CashPlan(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: 'Plan A',
            openingBalance: '500000.0000',
            currencyCode: 'JPY',
            notes: null,
            entries: $entries,
            createdBy: $this->userId,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<string> $amounts
     */
    private function entry(CashPlanCategory $category, string $label, array $amounts): CashPlanEntry
    {
        return new CashPlanEntry(
            id: $this->ulids->generate(),
            cashPlanId: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            category: $category,
            label: $label,
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
    }
}
