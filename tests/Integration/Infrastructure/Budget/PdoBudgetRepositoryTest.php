<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\Budget;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Infrastructure\Budget\PdoBudgetRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoBudgetRepository::class)]
final class PdoBudgetRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;
    private string $entityId = '';
    private string $fiscalTermId = '';
    private string $userId = '';
    private string $salesAccountId = '';
    private string $cogsAccountId = '';

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

    public function testRoundTripsHeaderAndLineItems(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBudgetRepository($pdo);
        $budget = $this->buildDraft([
            $this->line($this->salesAccountId, 0, array_fill(0, 12, '1500000.0000')),
            $this->line($this->cogsAccountId, 1, array_fill(0, 12, '300000.0000')),
        ]);
        $repo->save($budget);

        $loaded = $repo->findById($budget->id);
        self::assertNotNull($loaded);
        self::assertSame('Plan 2026', $loaded->name);
        self::assertCount(2, $loaded->lineItems);
        self::assertSame('1800000.0000', $loaded->monthlyTotal(1));
    }

    public function testDeleteIsSoftDelete(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBudgetRepository($pdo);
        $budget = $this->buildDraft([]);
        $repo->save($budget);

        $repo->delete($budget->id);
        self::assertNull($repo->findById($budget->id));
        self::assertSame([], $repo->findByEntity($this->entityId));
    }

    public function testApprovalStatePersistsAcrossReads(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBudgetRepository($pdo);
        $budget = $this->buildDraft([
            $this->line($this->salesAccountId, 0, array_fill(0, 12, '1500000.0000')),
        ]);
        $repo->save($budget);

        $approver = $this->userId;
        $approvedAt = new DateTimeImmutable('2026-05-01T00:00:00Z');
        $approved = $budget->approve($approver, $approvedAt);
        $repo->save($approved);

        $loaded = $repo->findById($budget->id);
        self::assertNotNull($loaded);
        self::assertSame(BudgetStatus::Approved, $loaded->status);
        self::assertSame($approver, $loaded->approvedBy);
        self::assertNotNull($loaded->approvedAt);
    }

    public function testFindByEntityFiltersByStatus(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoBudgetRepository($pdo);
        $draft = $this->buildDraft([], name: 'Draft Plan');
        $repo->save($draft);
        $approvable = $this->buildDraft([], name: 'Approved Plan');
        $repo->save($approvable);
        $approver = $this->userId;
        $approved = $approvable->approve($approver, new DateTimeImmutable('2026-05-01T00:00:00Z'));
        $repo->save($approved);

        $drafts = $repo->findByEntity($this->entityId, null, BudgetStatus::Draft, false);
        $approvedList = $repo->findByEntity($this->entityId, null, BudgetStatus::Approved, false);

        self::assertCount(1, $drafts);
        self::assertCount(1, $approvedList);
        self::assertSame('Draft Plan', $drafts[0]->name);
        self::assertSame('Approved Plan', $approvedList[0]->name);
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
        $this->salesAccountId = $this->ulids->generate();
        $this->cogsAccountId = $this->ulids->generate();
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
            . ' VALUES (:id, :e, 1, \'2026-04-01\', \'2027-03-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
        $this->insertAccountTitle($this->salesAccountId, '4000', '売上', 'revenue', 'credit');
        $this->insertAccountTitle($this->cogsAccountId, '5000', '仕入', 'expense', 'debit');
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
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, sort_order, is_active, created_at)'
            . ' VALUES (:id, :e, :c, :n, :cat, :ns, 0, 1, NOW(6))',
        )->execute([
            ':id'  => UlidGenerator::decode($id),
            ':e'   => UlidGenerator::decode($this->entityId),
            ':c'   => $code,
            ':n'   => $name,
            ':cat' => $category,
            ':ns'  => $normalSide,
        ]);
    }

    /**
     * @param list<BudgetLineItem> $items
     */
    private function buildDraft(array $items, string $name = 'Plan 2026'): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new Budget(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $name,
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
            notes: null,
            lineItems: $items,
            createdBy: $this->userId,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<string> $amounts
     */
    private function line(string $accountTitleId, int $sortOrder, array $amounts): BudgetLineItem
    {
        return new BudgetLineItem(
            id: $this->ulids->generate(),
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            accountTitleId: $accountTitleId,
            subAccountTitleId: null,
            sortOrder: $sortOrder,
            monthlyAmounts: $amounts,
        );
    }
}
