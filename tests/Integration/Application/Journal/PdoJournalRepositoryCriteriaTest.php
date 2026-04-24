<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Application\Journal;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\ValueObject\JournalDate;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;
use Rucaro\Infrastructure\Journal\PdoJournalRepository;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;

/**
 * Integration test for {@see PdoJournalRepository::findByCriteria}.
 *
 * Uses the same host-provided MariaDB as ConnectionFactoryIntegrationTest.
 * Skipped when no DB env is configured so Unit-suite developer laptops stay
 * green.
 */
#[CoversClass(PdoJournalRepository::class)]
final class PdoJournalRepositoryCriteriaTest extends TestCase
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

    public function testFindByCriteriaFiltersByEntityAndDate(): void
    {
        $repo = new PdoJournalRepository($this->requirePdo(), new UlidGenerator(new FrozenClock()));
        $entity = '01HW7K9B2QV7C8Y4ZENTITY0001';

        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000001', $entity, '2026-04-01'));
        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000002', $entity, '2026-04-15'));
        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000003', $entity, '2026-05-01'));

        $result = $repo->findByCriteria(new JournalSearchCriteria(
            entityId: $entity,
            from: JournalDate::fromString('2026-04-10'),
            to: JournalDate::fromString('2026-04-30'),
        ));

        self::assertSame(1, $result->total);
        self::assertCount(1, $result->items);
        self::assertSame('01HW7K9B2QV7C8Y4ZJRNL000002', $result->items[0]->id);
    }

    public function testFindByCriteriaFiltersByStatus(): void
    {
        $repo = new PdoJournalRepository($this->requirePdo(), new UlidGenerator(new FrozenClock()));
        $entity = '01HW7K9B2QV7C8Y4ZENTITY0001';

        $draft = $this->journal('01HW7K9B2QV7C8Y4ZJRNL000001', $entity, '2026-04-01');
        $approved = $this->journal('01HW7K9B2QV7C8Y4ZJRNL000002', $entity, '2026-04-02')
            ->approve(new DateTimeImmutable('2026-04-03T00:00:00Z'), '01HW7K9B2QV7C8Y4ZUSER000002');
        $repo->save($draft);
        $repo->save($approved);

        $result = $repo->findByCriteria(new JournalSearchCriteria(
            entityId: $entity,
            status: JournalStatus::Approved,
        ));

        self::assertSame(1, $result->total);
        self::assertSame('01HW7K9B2QV7C8Y4ZJRNL000002', $result->items[0]->id);
    }

    public function testFindByCriteriaFiltersByAccountTitle(): void
    {
        $repo = new PdoJournalRepository($this->requirePdo(), new UlidGenerator(new FrozenClock()));
        $entity = '01HW7K9B2QV7C8Y4ZENTITY0001';

        $repo->save($this->journal(
            '01HW7K9B2QV7C8Y4ZJRNL000001',
            $entity,
            '2026-04-01',
            '01HW7K9B2QV7C8Y4ZACCTTL0AA',
            '01HW7K9B2QV7C8Y4ZACCTTL0BB',
        ));
        $repo->save($this->journal(
            '01HW7K9B2QV7C8Y4ZJRNL000002',
            $entity,
            '2026-04-02',
            '01HW7K9B2QV7C8Y4ZACCTTL0CC',
            '01HW7K9B2QV7C8Y4ZACCTTL0DD',
        ));

        $result = $repo->findByCriteria(new JournalSearchCriteria(
            entityId: $entity,
            accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL0CC',
        ));

        self::assertSame(1, $result->total);
        self::assertSame('01HW7K9B2QV7C8Y4ZJRNL000002', $result->items[0]->id);
    }

    public function testSoftDeleteExcludesFromDefaultSearch(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoJournalRepository($pdo, new UlidGenerator(new FrozenClock()));
        $entity = '01HW7K9B2QV7C8Y4ZENTITY0001';

        $j = $this->journal('01HW7K9B2QV7C8Y4ZJRNL000001', $entity, '2026-04-01');
        $repo->save($j);
        $repo->delete($j->id, new DateTimeImmutable('2026-04-02T00:00:00Z'), '01HW7K9B2QV7C8Y4ZUSER000001');

        $defaultResult = $repo->findByCriteria(new JournalSearchCriteria(entityId: $entity));
        self::assertSame(0, $defaultResult->total);

        $inclusiveResult = $repo->findByCriteria(new JournalSearchCriteria(
            entityId: $entity,
            includeTrashed: true,
        ));
        self::assertSame(1, $inclusiveResult->total);
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
        // Minimal DDL sufficient for the Journal fixture. Kept inline so the
        // test does not depend on the Migration runner, which is still
        // landing in a parallel track.
        $pdo->exec('CREATE TABLE entities (id BINARY(16) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $pdo->exec('CREATE TABLE fiscal_terms (id BINARY(16) NOT NULL, entity_id BINARY(16) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $pdo->exec('CREATE TABLE users (id BINARY(16) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $pdo->exec('CREATE TABLE account_titles (id BINARY(16) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec(<<<'SQL'
            CREATE TABLE journal_entries (
              id BINARY(16) NOT NULL,
              entity_id BINARY(16) NOT NULL,
              fiscal_term_id BINARY(16) NOT NULL,
              journal_date DATE NOT NULL,
              booked_at TIMESTAMP(6) NOT NULL,
              summary VARCHAR(255) NOT NULL DEFAULT '',
              total_amount DECIMAL(18, 4) NOT NULL,
              currency_code CHAR(3) NOT NULL DEFAULT 'JPY',
              status VARCHAR(32) NOT NULL DEFAULT 'draft',
              source VARCHAR(16) NOT NULL DEFAULT 'manual',
              source_receipt_id BINARY(16) NULL,
              created_by BINARY(16) NOT NULL,
              approved_by BINARY(16) NULL,
              approved_at TIMESTAMP(6) NULL,
              created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              updated_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
              deleted_at TIMESTAMP(6) NULL,
              PRIMARY KEY (id),
              KEY idx_journal__entity_date (entity_id, journal_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $pdo->exec(<<<'SQL'
            CREATE TABLE journal_entry_lines (
              id BINARY(16) NOT NULL,
              entry_id BINARY(16) NOT NULL,
              line_no SMALLINT NOT NULL,
              side VARCHAR(6) NOT NULL,
              account_title_id BINARY(16) NOT NULL,
              sub_account_title_id BINARY(16) NULL,
              amount DECIMAL(18, 4) NOT NULL,
              tax_rate_percent DECIMAL(5, 2) NOT NULL DEFAULT 0,
              tax_amount DECIMAL(18, 4) NOT NULL DEFAULT 0,
              is_tax_reduced TINYINT(1) NOT NULL DEFAULT 0,
              memo VARCHAR(255) NOT NULL DEFAULT '',
              booked_at TIMESTAMP(6) NOT NULL,
              created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              updated_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
              PRIMARY KEY (id),
              UNIQUE KEY uq_line (entry_id, line_no),
              KEY idx_account (account_title_id, booked_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);
    }

    private function journal(
        string $id,
        string $entityId,
        string $date,
        string $debitAccount = '01HW7K9B2QV7C8Y4ZACCTTL001',
        string $creditAccount = '01HW7K9B2QV7C8Y4ZACCTTL002',
    ): Journal {
        $tz = new DateTimeZone('UTC');
        $ts = new DateTimeImmutable($date . 'T12:00:00', $tz);
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE' . substr($id, -4) . 'D',
                lineNo: 1,
                side: 'debit',
                accountTitleId: $debitAccount,
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE' . substr($id, -4) . 'C',
                lineNo: 2,
                side: 'credit',
                accountTitleId: $creditAccount,
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
        ];
        return new Journal(
            id: $id,
            entityId: $entityId,
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable($date, $tz),
            bookedAt: $ts,
            summary: 'Test ' . $id,
            totalAmount: '100.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: $ts,
            updatedAt: $ts,
            deletedAt: null,
            lines: $lines,
        );
    }
}
