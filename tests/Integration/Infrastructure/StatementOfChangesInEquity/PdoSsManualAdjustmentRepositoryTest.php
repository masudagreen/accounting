<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\StatementOfChangesInEquity;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\StatementOfChangesInEquity\PdoSsManualAdjustmentRepository;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoSsManualAdjustmentRepository::class)]
final class PdoSsManualAdjustmentRepositoryTest extends TestCase
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

    public function testRoundTripsSingleRow(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoSsManualAdjustmentRepository($pdo);
        $adj = $this->makeAdjustment(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '5000000.0000', 0, 'Rights issue');
        $repo->save($adj);

        $loaded = $repo->findById($adj->id);
        self::assertNotNull($loaded);
        self::assertSame(SsSectionCode::CapitalStock, $loaded->sectionCode);
        self::assertSame(SsChangeType::NewIssue, $loaded->changeType);
        self::assertSame('5000000.0000', $loaded->amount);
        self::assertSame('Rights issue', $loaded->label);
    }

    public function testListByEntityAndFiscalTermIsOrderedBySortOrder(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoSsManualAdjustmentRepository($pdo);
        $repo->save($this->makeAdjustment(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '1.0000', 5, 'later'));
        $repo->save($this->makeAdjustment(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '2.0000', 1, 'earlier'));
        $rows = $repo->findByEntityAndFiscalTerm($this->entityId, $this->fiscalTermId);
        self::assertCount(2, $rows);
        self::assertSame('earlier', $rows[0]->label);
        self::assertSame('later', $rows[1]->label);
    }

    public function testSaveUpdatesExistingRowViaOnDuplicateKey(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoSsManualAdjustmentRepository($pdo);
        $adj = $this->makeAdjustment(SsSectionCode::RetainedEarnings, SsChangeType::Dividend, '-1000000.0000', 0, 'Initial');
        $repo->save($adj);
        $repo->save($adj->with(amount: '-5000000.0000', label: 'Revised'));
        $loaded = $repo->findById($adj->id);
        self::assertNotNull($loaded);
        self::assertSame('-5000000.0000', $loaded->amount);
        self::assertSame('Revised', $loaded->label);
    }

    public function testDeleteRemovesRow(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoSsManualAdjustmentRepository($pdo);
        $adj = $this->makeAdjustment(SsSectionCode::RetainedEarnings, SsChangeType::Dividend, '-500000.0000', 0, 'One-off');
        $repo->save($adj);
        $repo->delete($adj->id);
        self::assertNull($repo->findById($adj->id));
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
            . ' VALUES (:id, :e, 1, \'2026-04-01\', \'2027-03-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
    }

    private function makeAdjustment(
        SsSectionCode $section,
        SsChangeType $type,
        string $amount,
        int $sortOrder,
        string $label,
    ): SsManualAdjustment {
        return new SsManualAdjustment(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            sectionCode: $section,
            changeType: $type,
            amount: $amount,
            label: $label,
            sortOrder: $sortOrder,
            notes: null,
        );
    }
}
