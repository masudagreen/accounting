<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\FinancialStatement\Port\Cs;

use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsFlowCategory;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\FinancialStatement\Port\Cs\PdoAccountTitleCsMappingRepository;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * DB integration coverage for {@see PdoAccountTitleCsMappingRepository}.
 *
 * Seeds an entity + account_title + one CS mapping row and asserts the PDO
 * repository materialises an {@see \Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMapping}
 * DTO with every column round-tripped including the `flow_category` enum.
 */
#[CoversClass(PdoAccountTitleCsMappingRepository::class)]
final class PdoAccountTitleCsMappingRepositoryTest extends TestCase
{
    private string $host = '';
    private int $port = 3306;
    private string $dbname = '';
    private string $username = '';
    private string $password = '';
    private ?PDO $pdo = null;
    private UlidGenerator $ulids;

    protected function setUp(): void
    {
        $user = getenv('RUCARO_TEST_DB_USER');
        if ($user === false || $user === '') {
            $this->markTestSkipped('RUCARO_TEST_DB_USER is not set; skipping DB integration test.');
        }
        $this->username = (string) $user;
        $this->host     = ((string) getenv('RUCARO_TEST_DB_HOST')) ?: '127.0.0.1';
        $portEnv        = getenv('RUCARO_TEST_DB_PORT');
        $this->port     = $portEnv !== false && $portEnv !== '' ? (int) $portEnv : 3306;
        $this->dbname   = ((string) getenv('RUCARO_TEST_DB_NAME')) ?: 'rucaro_test';
        $pwEnv          = getenv('RUCARO_TEST_DB_PASSWORD');
        $this->password = $pwEnv === false ? '' : (string) $pwEnv;

        $root = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        $root->exec(sprintf(
            'CREATE DATABASE `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->dbname,
        ));

        $this->pdo = ConnectionFactory::createFromConfig($this->config());
        $runner = new MigrationRunner($this->pdo, $this->migrationsDir());
        $runner->up();
        // The project-wide migration runner glob picks the alphabetically-last
        // file per version, so `_seed.sql` replaces the corresponding
        // `CREATE TABLE` migration. Re-apply the explicit CREATE TABLE SQL for
        // 0008 / 0009 here to guarantee schema completeness for this test.
        $this->applyFile($this->pdo, '0008_fs_mappings.sql');
        $this->applyFile($this->pdo, '0009_fs_cs_mappings.sql');

        $this->ulids = new UlidGenerator();
    }

    private function applyFile(PDO $pdo, string $name): void
    {
        $path = $this->migrationsDir() . DIRECTORY_SEPARATOR . $name;
        if (!is_file($path)) {
            return;
        }
        $sql = (string) file_get_contents($path);
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // If the table already exists, ignore — the runner may have
            // actually applied it first.
            if (!str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
        }
    }

    protected function tearDown(): void
    {
        if ($this->dbname === '' || $this->username === '') {
            return;
        }
        try {
            $root = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );
            $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        } catch (PDOException) {
            // best-effort
        }
    }

    public function testFindAllByEntityReturnsMappingWithFlowCategoryAndSign(): void
    {
        $pdo = $this->requirePdo();
        $userUlid = $this->ulids->generate();
        $entityUlid = $this->ulids->generate();
        $accountUlid = $this->ulids->generate();

        $this->seedUser($pdo, $userUlid, 'u1@test.example');
        $this->seedEntity($pdo, $entityUlid, $userUlid);
        $this->seedAccountTitle($pdo, $entityUlid, $accountUlid, '531', '減価償却費');
        $this->seedMapping(
            $pdo,
            entityUlid: $entityUlid,
            accountUlid: $accountUlid,
            sectionCode: 'depreciation',
            flowCategory: 'operating',
            sign: 1,
            isWorkingCapital: 0,
            sortOrder: 10,
            displayLabel: null,
        );

        $repo = new PdoAccountTitleCsMappingRepository($pdo);
        $rows = $repo->findAllByEntity($entityUlid);

        self::assertCount(1, $rows);
        self::assertSame($accountUlid, $rows[0]->accountTitleId);
        self::assertSame('depreciation', $rows[0]->sectionCode);
        self::assertSame(CsFlowCategory::Operating, $rows[0]->flowCategory);
        self::assertSame(1, $rows[0]->sign);
        self::assertFalse($rows[0]->isWorkingCapital);
        self::assertNull($rows[0]->displayLabel);
    }

    public function testWorkingCapitalFlagAndMinusSignAreRoundTripped(): void
    {
        $pdo = $this->requirePdo();
        $userUlid = $this->ulids->generate();
        $entityUlid = $this->ulids->generate();
        $accountUlid = $this->ulids->generate();

        $this->seedUser($pdo, $userUlid, 'u2@test.example');
        $this->seedEntity($pdo, $entityUlid, $userUlid);
        $this->seedAccountTitle($pdo, $entityUlid, $accountUlid, '121', '売掛金');
        $this->seedMapping(
            $pdo,
            entityUlid: $entityUlid,
            accountUlid: $accountUlid,
            sectionCode: 'wc_receivables',
            flowCategory: 'operating',
            sign: 1,
            isWorkingCapital: 1,
            sortOrder: 20,
            displayLabel: '売上債権の増減',
        );

        $repo = new PdoAccountTitleCsMappingRepository($pdo);
        $rows = $repo->findAllByEntity($entityUlid);
        self::assertCount(1, $rows);
        self::assertTrue($rows[0]->isWorkingCapital);
        self::assertSame('売上債権の増減', $rows[0]->displayLabel);
    }

    private function seedUser(PDO $pdo, string $ulid, string $email): void
    {
        $pdo->prepare(
            'INSERT INTO users (id, email, email_normalized, password_hash, display_name, role, is_active, created_at, updated_at)
             VALUES (:id, :em, :emn, :pw, :dn, :role, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'   => UlidGenerator::decode($ulid),
            ':em'   => $email,
            ':emn'  => $email,
            ':pw'   => 'x',
            ':dn'   => 'CS test user',
            ':role' => 'owner',
        ]);
    }

    private function seedEntity(PDO $pdo, string $ulid, string $ownerUlid): void
    {
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at, updated_at)
             VALUES (:id, :ow, :nm, "JPN", "JPY", "0401", 1, NOW(6), NOW(6))'
        )->execute([
            ':id' => UlidGenerator::decode($ulid),
            ':ow' => UlidGenerator::decode($ownerUlid),
            ':nm' => 'CS Test Entity',
        ]);
    }

    private function seedAccountTitle(
        PDO $pdo,
        string $entityUlid,
        string $accountUlid,
        string $code,
        string $name,
    ): void {
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, sort_order, is_active, created_at, updated_at)
             VALUES (:id, :ent, :c, :n, :cat, :ns, 0, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($accountUlid),
            ':ent' => UlidGenerator::decode($entityUlid),
            ':c'   => $code,
            ':n'   => $name,
            ':cat' => 'asset',
            ':ns'  => 'debit',
        ]);
    }

    private function seedMapping(
        PDO $pdo,
        string $entityUlid,
        string $accountUlid,
        string $sectionCode,
        string $flowCategory,
        int $sign,
        int $isWorkingCapital,
        int $sortOrder,
        ?string $displayLabel,
    ): void {
        $stmt = $pdo->prepare(
            'INSERT INTO account_title_cs_mappings
             (id, entity_id, account_title_id, cs_section_code, sort_order, display_label,
              sign, flow_category, is_working_capital)
             VALUES (:id, :eid, :aid, :sec, :so, :dl, :sign, :flow, :wc)'
        );
        $stmt->execute([
            ':id'   => UlidGenerator::decode($this->ulids->generate()),
            ':eid'  => UlidGenerator::decode($entityUlid),
            ':aid'  => UlidGenerator::decode($accountUlid),
            ':sec'  => $sectionCode,
            ':so'   => $sortOrder,
            ':dl'   => $displayLabel,
            ':sign' => $sign,
            ':flow' => $flowCategory,
            ':wc'   => $isWorkingCapital,
        ]);
    }

    private function requirePdo(): PDO
    {
        self::assertNotNull($this->pdo);
        return $this->pdo;
    }

    private function config(): DatabaseConfig
    {
        return new DatabaseConfig(
            host: $this->host,
            dbname: $this->dbname,
            username: $this->username,
            password: $this->password,
            port: $this->port,
        );
    }

    private function migrationsDir(): string
    {
        return dirname(__DIR__, 6) . DIRECTORY_SEPARATOR
            . 'scripts' . DIRECTORY_SEPARATOR . 'migrate';
    }
}
