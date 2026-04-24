<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\FinancialStatementNotes;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Infrastructure\FinancialStatementNotes\PdoFsNoteRepository;
use Rucaro\Infrastructure\FinancialStatementNotes\PdoFsNoteTemplateRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoFsNoteRepository::class)]
#[CoversClass(PdoFsNoteTemplateRepository::class)]
final class PdoFsNoteRepositoryTest extends TestCase
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

    public function testInsertAndRead(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoFsNoteRepository($pdo);
        $note = $this->buildNote(label: 'Test label');
        $repo->save($note);

        $loaded = $repo->findById($note->id);
        self::assertNotNull($loaded);
        self::assertSame('Test label', $loaded->label);
        self::assertSame(FsNoteCategory::AccountingPolicy, $loaded->category);
    }

    public function testFindByEntityAndTermFiltersActive(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoFsNoteRepository($pdo);
        $active = $this->buildNote(label: 'Active');
        $inactive = $this->buildNote(label: 'Inactive', isActive: false);
        $repo->save($active);
        $repo->save($inactive);

        $all = $repo->findByEntityAndTerm($this->entityId, $this->fiscalTermId, false);
        $onlyActive = $repo->findByEntityAndTerm($this->entityId, $this->fiscalTermId, true);
        self::assertCount(2, $all);
        self::assertCount(1, $onlyActive);
    }

    public function testDeleteRemovesRow(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoFsNoteRepository($pdo);
        $note = $this->buildNote();
        $repo->save($note);
        $repo->delete($note->id);
        self::assertNull($repo->findById($note->id));
    }

    public function testCountByTemplateCode(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoFsNoteRepository($pdo);
        $repo->save($this->buildNote(templateCode: 'AP_INVENTORY'));
        $repo->save($this->buildNote(templateCode: 'AP_DEPRECIATION', label: 'DepLabel'));
        self::assertSame(1, $repo->countByTemplateCode($this->entityId, $this->fiscalTermId, 'AP_INVENTORY'));
        self::assertSame(0, $repo->countByTemplateCode($this->entityId, $this->fiscalTermId, 'NOPE'));
    }

    public function testTemplatesSeedsAreReadable(): void
    {
        $pdo = $this->requirePdo();
        $seedPath = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'migrate' . DIRECTORY_SEPARATOR . '0018_fs_notes_seed.sql';
        $sql = file_get_contents($seedPath);
        self::assertNotFalse($sql);
        $pdo->exec($sql);
        $repo = new PdoFsNoteTemplateRepository($pdo);
        $all = $repo->findAll();
        self::assertGreaterThanOrEqual(15, count($all));
        self::assertNotNull($repo->findByCode('AP_INVENTORY'));
        self::assertCount(2, $repo->findByCodes(['AP_INVENTORY', 'AP_DEPRECIATION']));
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
                ':e'  => 'fs-notes@example.com',
                ':d'  => 'Tester',
                ':p'  => 'x',
            ]);
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at)'
            . ' VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0401\', 1, NOW(6))',
        )->execute([
            ':id'    => UlidGenerator::decode($this->entityId),
            ':owner' => UlidGenerator::decode($this->userId),
            ':n'     => 'FS Notes Entity',
        ]);
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at)'
            . ' VALUES (:id, :e, 1, \'2026-04-01\', \'2027-03-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
    }

    private function buildNote(
        string $label = 'L',
        string $body = 'B',
        ?string $templateCode = null,
        bool $isActive = true,
    ): FinancialStatementNote {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z');
        return new FinancialStatementNote(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            templateCode: $templateCode,
            category: FsNoteCategory::AccountingPolicy,
            label: $label,
            body: $body,
            sortOrder: 0,
            isActive: $isActive,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
