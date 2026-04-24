<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
use Rucaro\Infrastructure\ConsumptionTax\PdoAccountTitleConsumptionTaxDefaultRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxCategoryRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxPeriodRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxRateRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoInvoiceRegistrationRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

#[CoversClass(PdoConsumptionTaxRateRepository::class)]
#[CoversClass(PdoConsumptionTaxCategoryRepository::class)]
#[CoversClass(PdoAccountTitleConsumptionTaxDefaultRepository::class)]
#[CoversClass(PdoInvoiceRegistrationRepository::class)]
#[CoversClass(PdoConsumptionTaxPeriodRepository::class)]
final class PdoConsumptionTaxRepositoryTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;
    private string $entityId = '';
    private string $fiscalTermId = '';
    private string $accountTitleId = '';

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
        $migrationsDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'migrate';
        $runner = new MigrationRunner($this->pdo, $migrationsDir);
        $runner->up();
        // The project-wide MigrationRunner picks the alphabetically-last
        // file per version, so `_seed.sql` replaces the corresponding
        // `CREATE TABLE` migration. Re-apply explicitly so both exist.
        $this->applyFile($this->pdo, $migrationsDir, '0014_consumption_tax.sql');
        $this->applyFile($this->pdo, $migrationsDir, '0014_consumption_tax_seed.sql');
        $this->ulids = new UlidGenerator();
        $this->seedFixtures();
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null && $this->dbName !== '') {
            $this->pdo->exec("DROP DATABASE IF EXISTS `$this->dbName`");
        }
    }

    public function testSeedRatesAreLoaded(): void
    {
        $repo = new PdoConsumptionTaxRateRepository($this->requirePdo());
        $rates = $repo->findAll();
        self::assertGreaterThanOrEqual(8, count($rates), 'seed should insert 8 rate rows');

        $standard10 = $repo->findByCode('standard_10');
        self::assertNotNull($standard10);
        self::assertSame('10.00', $standard10->ratePercent);
        self::assertFalse($standard10->isReduced);
        self::assertTrue($standard10->isTaxable);
    }

    public function testFindEffectiveOnWindowsToCurrentRates(): void
    {
        $repo = new PdoConsumptionTaxRateRepository($this->requirePdo());
        $active = $repo->findEffectiveOn(new DateTimeImmutable('2026-05-15'));
        $codes = array_map(static fn ($r) => $r->code, $active);
        self::assertContains('standard_10', $codes);
        self::assertContains('reduced_8', $codes);
        self::assertNotContains('old_3', $codes, 'old 3% must not be effective in 2026');
    }

    public function testSeedCategoriesAreLoaded(): void
    {
        $repo = new PdoConsumptionTaxCategoryRepository($this->requirePdo());
        $cats = $repo->findAll();
        self::assertGreaterThanOrEqual(9, count($cats));
        $taxableSales = $repo->findByCode(ConsumptionTaxCategoryCode::TaxableSales);
        self::assertNotNull($taxableSales);
        self::assertSame('sales', $taxableSales->side);
    }

    public function testAccountTitleTaxDefaultRoundTrip(): void
    {
        $repo = new PdoAccountTitleConsumptionTaxDefaultRepository($this->requirePdo());
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $row = new AccountTitleConsumptionTaxDefault(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            accountTitleId: $this->accountTitleId,
            defaultCategoryCode: ConsumptionTaxCategoryCode::TaxableSales,
            defaultRateCode: 'standard_10',
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($row);
        $loaded = $repo->findByAccountTitle($this->entityId, $this->accountTitleId);
        self::assertNotNull($loaded);
        self::assertSame(ConsumptionTaxCategoryCode::TaxableSales, $loaded->defaultCategoryCode);
        self::assertSame('standard_10', $loaded->defaultRateCode);
    }

    public function testInvoiceRegistrationRoundTrip(): void
    {
        $repo = new PdoInvoiceRegistrationRepository($this->requirePdo());
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $reg = new InvoiceRegistration(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            counterpartyName: 'ACME商事',
            registrationNumber: 'T1234567890123',
            isRegistered: true,
            registeredFrom: new DateTimeImmutable('2023-10-01'),
            registeredUntil: null,
            notes: 'seed',
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($reg);
        $found = $repo->findByRegistrationNumber($this->entityId, 'T1234567890123');
        self::assertNotNull($found);
        self::assertSame('ACME商事', $found->counterpartyName);
    }

    public function testPeriodRoundTrip(): void
    {
        $repo = new PdoConsumptionTaxPeriodRepository($this->requirePdo());
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $period = new ConsumptionTaxPeriod(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            periodFrom: new DateTimeImmutable('2026-04-01'),
            periodTo: new DateTimeImmutable('2027-03-31'),
            calculationMethod: ConsumptionTaxCalculationMethod::Simplified,
            simplifiedBusinessCategory: SimplifiedBusinessCategory::Wholesale,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($period);
        $found = $repo->findById($period->id);
        self::assertNotNull($found);
        self::assertSame(ConsumptionTaxCalculationMethod::Simplified, $found->calculationMethod);
        self::assertSame(SimplifiedBusinessCategory::Wholesale, $found->simplifiedBusinessCategory);

        $all = $repo->findByEntity($this->entityId);
        self::assertCount(1, $all);
    }

    private function requirePdo(): PDO
    {
        if ($this->pdo === null) {
            $this->fail('PDO not initialised.');
        }
        return $this->pdo;
    }

    private function seedFixtures(): void
    {
        $pdo = $this->requirePdo();
        $userId = $this->ulids->generate();
        $this->entityId = $this->ulids->generate();
        $this->fiscalTermId = $this->ulids->generate();
        $this->accountTitleId = $this->ulids->generate();

        $pdo->prepare('INSERT INTO users (id, email, display_name, password_hash, is_active, created_at) VALUES (:id, :e, :d, :p, 1, NOW(6))')
            ->execute([
                ':id' => UlidGenerator::decode($userId),
                ':e'  => 'tester@example.com',
                ':d'  => 'Tester',
                ':p'  => 'x',
            ]);
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at)'
            . ' VALUES (:id, :owner, :n, \'JPN\', \'JPY\', \'0401\', 1, NOW(6))',
        )->execute([
            ':id'    => UlidGenerator::decode($this->entityId),
            ':owner' => UlidGenerator::decode($userId),
            ':n'     => 'Test Entity',
        ]);
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at)'
            . ' VALUES (:id, :e, 1, \'2026-04-01\', \'2027-03-31\', 0, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->fiscalTermId),
            ':e'  => UlidGenerator::decode($this->entityId),
        ]);
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, parent_id, sort_order, is_active, created_at)'
            . ' VALUES (:id, :e, :c, :n, \'revenue\', \'credit\', NULL, 100, 1, NOW(6))',
        )->execute([
            ':id' => UlidGenerator::decode($this->accountTitleId),
            ':e'  => UlidGenerator::decode($this->entityId),
            ':c'  => '4000',
            ':n'  => '売上高',
        ]);
    }

    private function applyFile(PDO $pdo, string $dir, string $name): void
    {
        $path = $dir . DIRECTORY_SEPARATOR . $name;
        if (!is_file($path)) {
            return;
        }
        $sql = (string) file_get_contents($path);
        try {
            // Run the whole file as a multi-statement blob. MariaDB allows
            // this via `PDO::exec` on connections that don't disable it.
            $stmts = preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [];
            foreach ($stmts as $stmt) {
                $stmt = trim($stmt);
                if ($stmt === '' || str_starts_with($stmt, '--')) {
                    continue;
                }
                try {
                    $pdo->exec($stmt);
                } catch (PDOException $e) {
                    if (!str_contains($e->getMessage(), 'already exists')
                        && !str_contains($e->getMessage(), 'Duplicate entry')
                    ) {
                        throw $e;
                    }
                }
            }
        } catch (PDOException $e) {
            if (!str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
        }
    }
}
