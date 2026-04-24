<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\FinancialStatement\Port\Cs;

use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\FinancialStatement\Port\Cs\PdoCsSectionDefinitionRepository;

/**
 * DB integration coverage for {@see PdoCsSectionDefinitionRepository}.
 *
 * Mirrors the existing PdoApprovalTokenRepository integration style:
 * uses host-provided MariaDB, skips cleanly without RUCARO_TEST_DB_USER.
 */
#[CoversClass(PdoCsSectionDefinitionRepository::class)]
final class PdoCsSectionDefinitionRepositoryTest extends TestCase
{
    private string $host = '';
    private int $port = 3306;
    private string $dbname = '';
    private string $username = '';
    private string $password = '';
    private ?PDO $pdo = null;

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
        $this->migrate($this->pdo);
        $this->seed($this->pdo);
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
            // best-effort cleanup
        }
    }

    public function testFindAllReturnsSeededSectionsOrderedBySortOrder(): void
    {
        $repo = new PdoCsSectionDefinitionRepository($this->requirePdo());
        $defs = $repo->findAll();

        self::assertNotEmpty($defs);
        // Ordered by sort_order ascending.
        for ($i = 1; $i < count($defs); $i++) {
            self::assertGreaterThanOrEqual(
                $defs[$i - 1]->sortOrder,
                $defs[$i]->sortOrder,
                sprintf('Expected sort_order ascending between %s and %s', $defs[$i - 1]->code, $defs[$i]->code),
            );
        }

        // First entry is the operating_cf tree root.
        self::assertSame('operating_cf', $defs[0]->code);
        self::assertNull($defs[0]->parentCode);

        // ending_cash is a total with a formula.
        $endingCash = self::find($defs, 'ending_cash');
        self::assertTrue($endingCash->isTotal);
        self::assertSame('+net_change_in_cash+beginning_cash', $endingCash->formula);
    }

    public function testFormulaParsingRoundTripsThroughDb(): void
    {
        $repo = new PdoCsSectionDefinitionRepository($this->requirePdo());
        $defs = $repo->findAll();
        $operating = self::find($defs, 'operating_cf_total');
        self::assertSame(
            [
                [1, 'operating_cf_subtotal'],
                [1, 'interest_received'],
                [-1, 'interest_paid'],
                [-1, 'tax_paid'],
            ],
            $operating->parsedFormula(),
        );
    }

    /**
     * @param list<\Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition> $defs
     */
    private static function find(array $defs, string $code): \Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition
    {
        foreach ($defs as $d) {
            if ($d->code === $code) {
                return $d;
            }
        }
        throw new \RuntimeException("Missing section: $code");
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

    private function migrate(PDO $pdo): void
    {
        $sql = (string) file_get_contents(
            dirname(__DIR__, 6) . DIRECTORY_SEPARATOR
            . 'scripts' . DIRECTORY_SEPARATOR
            . 'migrate' . DIRECTORY_SEPARATOR
            . '0009_fs_cs_mappings.sql'
        );
        // This test does not need the account_title_cs_mappings FK targets
        // (entities / account_titles) so trim the second statement's FKs
        // by executing only the section definition table.
        $sections = $this->extractStatement($sql, 'fs_cs_section_definitions');
        $pdo->exec($sections);
    }

    private function seed(PDO $pdo): void
    {
        $sql = (string) file_get_contents(
            dirname(__DIR__, 6) . DIRECTORY_SEPARATOR
            . 'scripts' . DIRECTORY_SEPARATOR
            . 'migrate' . DIRECTORY_SEPARATOR
            . '0009_fs_cs_mappings_seed.sql'
        );
        $pdo->exec($sql);
    }

    /**
     * Pull out the CREATE TABLE statement for one table from a mixed migration
     * SQL file so this test does not need to create the entities/account_titles
     * FK targets.
     */
    private function extractStatement(string $sql, string $table): string
    {
        $pattern = '/CREATE TABLE ' . preg_quote($table, '/') . '.*?;/s';
        if (preg_match($pattern, $sql, $m) !== 1) {
            throw new \RuntimeException("CREATE TABLE for $table not found");
        }
        return $m[0];
    }
}
