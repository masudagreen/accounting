<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\TrialBalance\PdoTrialBalanceQueryService;
use Rucaro\Infrastructure\TrialBalance\PdoTrialBalanceSnapshotRepository;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Integration tests for the PDO-backed TrialBalance infrastructure.
 *
 * Requires env:
 *   RUCARO_TEST_DB_DSN   e.g. "mysql:host=127.0.0.1;port=3306"
 *   RUCARO_TEST_DB_USER
 *   RUCARO_TEST_DB_PASS  (may be empty)
 *   RUCARO_TEST_DB_NAME  (default rucaro_test)
 *
 * Skips cleanly when any of the required env vars is missing, so
 * `phpunit --testsuite=Unit` stays green without a live database.
 */
#[CoversClass(PdoTrialBalanceQueryService::class)]
#[CoversClass(PdoTrialBalanceSnapshotRepository::class)]
final class PdoTrialBalanceQueryServiceTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;

    private string $entityId = '';
    private string $fiscalTermId = '';
    private string $cashAccountId = '';
    private string $salesAccountId = '';
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
        $this->pdo->exec("SET NAMES utf8mb4");
        $this->pdo->exec("SET time_zone = '+00:00'");

        $runner = new MigrationRunner(
            $this->pdo,
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'migrate',
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

    public function testQueryByPeriodSumsPostedLinesGroupedByAccount(): void
    {
        $svc = new PdoTrialBalanceQueryService($this->requirePdo());

        $tb = $svc->queryByPeriod(
            $this->entityId,
            $this->fiscalTermId,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        self::assertCount(2, $tb->rows);
        self::assertSame('8000.0000', $tb->debitTotal());
        self::assertSame('8000.0000', $tb->creditTotal());
        self::assertTrue($tb->isBalanced());

        // Sorted by account code
        self::assertSame('101', $tb->rows[0]->accountTitleCode);
        self::assertSame('401', $tb->rows[1]->accountTitleCode);

        // Cash is debit-normal
        self::assertSame('8000.0000', $tb->rows[0]->debitTotal);
        self::assertSame('0.0000',    $tb->rows[0]->creditTotal);
        self::assertSame('8000.0000', $tb->rows[0]->balance);
    }

    public function testQueryByPeriodExcludesDraftAndDeletedEntries(): void
    {
        // Insert one draft entry that must not count.
        $pdo = $this->requirePdo();
        $draftId = $this->ulids->generate();
        $this->insertJournalEntry($draftId, 'draft', '2026-04-15', '999.0000', null);
        $this->insertJournalLine($draftId, 1, 'debit',  $this->cashAccountId,  '999.0000');
        $this->insertJournalLine($draftId, 2, 'credit', $this->salesAccountId, '999.0000');

        // Soft-deleted entry
        $deletedId = $this->ulids->generate();
        $this->insertJournalEntry($deletedId, 'posted', '2026-04-16', '777.0000', '2026-04-17 00:00:00.000000');
        $this->insertJournalLine($deletedId, 1, 'debit',  $this->cashAccountId,  '777.0000');
        $this->insertJournalLine($deletedId, 2, 'credit', $this->salesAccountId, '777.0000');

        $svc = new PdoTrialBalanceQueryService($pdo);
        $tb = $svc->queryByPeriod(
            $this->entityId,
            $this->fiscalTermId,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        // Only the two posted, non-deleted seed entries (8000 each side) count.
        self::assertSame('8000.0000', $tb->debitTotal());
    }

    public function testSnapshotRepositoryRoundTrip(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoTrialBalanceSnapshotRepository($pdo);

        $monthEnd = new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC'));
        $generatedAt = new DateTimeImmutable('2026-04-30T12:00:00Z', new DateTimeZone('UTC'));

        $repo->saveAll([
            new TrialBalanceSnapshot(
                id: $this->ulids->generate(),
                entityId: $this->entityId,
                fiscalTermId: $this->fiscalTermId,
                snapshotDate: $monthEnd,
                accountTitleId: $this->cashAccountId,
                debitTotal: '8000.0000',
                creditTotal: '0.0000',
                balance: '8000.0000',
                lineCount: 2,
                generatedAt: $generatedAt,
            ),
        ]);

        $found = $repo->findByMonth($this->entityId, $this->fiscalTermId, $monthEnd);
        self::assertCount(1, $found);
        self::assertSame('8000.0000', $found[0]->debitTotal);

        $repo->deleteByMonth($this->entityId, $this->fiscalTermId, $monthEnd);
        self::assertSame([], $repo->findByMonth($this->entityId, $this->fiscalTermId, $monthEnd));
    }

    public function testLatestSnapshotDateReflectsLatestPersistedMonth(): void
    {
        $pdo = $this->requirePdo();
        $repo = new PdoTrialBalanceSnapshotRepository($pdo);
        $svc = new PdoTrialBalanceQueryService($pdo);

        self::assertNull($svc->latestSnapshotDate($this->entityId, $this->fiscalTermId));

        $repo->saveAll([
            $this->buildSnapshot('2026-04-30', '100.0000', '100.0000'),
            $this->buildSnapshot('2026-05-31', '200.0000', '200.0000'),
        ]);

        $latest = $svc->latestSnapshotDate($this->entityId, $this->fiscalTermId);
        self::assertNotNull($latest);
        self::assertSame('2026-05-31', $latest->format('Y-m-d'));
    }

    private function buildSnapshot(string $date, string $debit, string $credit): TrialBalanceSnapshot
    {
        return new TrialBalanceSnapshot(
            id: $this->ulids->generate(),
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            snapshotDate: new DateTimeImmutable($date, new DateTimeZone('UTC')),
            accountTitleId: $this->cashAccountId,
            debitTotal: $debit,
            creditTotal: $credit,
            balance: $debit,
            lineCount: 1,
            generatedAt: new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }

    // ----------------------------------------------------------------
    // Seeding helpers
    // ----------------------------------------------------------------

    private function seed(): void
    {
        $pdo = $this->requirePdo();

        $this->userId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO users (id, email, email_normalized, password_hash, display_name, role, is_active, created_at, updated_at)
             VALUES (:id, :em, :emn, :pw, :dn, :role, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($this->userId),
            ':em'  => 'tb-test@example.com',
            ':emn' => 'tb-test@example.com',
            ':pw'  => 'x',
            ':dn'  => 'TB test',
            ':role' => 'owner',
        ]);

        $this->entityId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at, updated_at)
             VALUES (:id, :ow, :nm, "JPN", "JPY", "0401", 1, NOW(6), NOW(6))'
        )->execute([
            ':id' => UlidGenerator::decode($this->entityId),
            ':ow' => UlidGenerator::decode($this->userId),
            ':nm' => 'TB Entity',
        ]);

        $this->fiscalTermId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at, updated_at)
             VALUES (:id, :ent, 1, "2026-04-01", "2027-03-31", 0, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($this->fiscalTermId),
            ':ent' => UlidGenerator::decode($this->entityId),
        ]);

        $this->cashAccountId = $this->ulids->generate();
        $this->salesAccountId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, sort_order, is_active, created_at, updated_at)
             VALUES (:id, :ent, :c, :n, :cat, :ns, :so, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($this->cashAccountId),
            ':ent' => UlidGenerator::decode($this->entityId),
            ':c'   => '101',
            ':n'   => '現金',
            ':cat' => 'asset',
            ':ns'  => 'debit',
            ':so'  => 1,
        ]);
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, sort_order, is_active, created_at, updated_at)
             VALUES (:id, :ent, :c, :n, :cat, :ns, :so, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($this->salesAccountId),
            ':ent' => UlidGenerator::decode($this->entityId),
            ':c'   => '401',
            ':n'   => '売上',
            ':cat' => 'revenue',
            ':ns'  => 'credit',
            ':so'  => 2,
        ]);

        $postedA = $this->ulids->generate();
        $this->insertJournalEntry($postedA, 'posted', '2026-04-05', '5000.0000', null);
        $this->insertJournalLine($postedA, 1, 'debit',  $this->cashAccountId,  '5000.0000');
        $this->insertJournalLine($postedA, 2, 'credit', $this->salesAccountId, '5000.0000');

        $postedB = $this->ulids->generate();
        $this->insertJournalEntry($postedB, 'posted', '2026-04-20', '3000.0000', null);
        $this->insertJournalLine($postedB, 1, 'debit',  $this->cashAccountId,  '3000.0000');
        $this->insertJournalLine($postedB, 2, 'credit', $this->salesAccountId, '3000.0000');
    }

    private function insertJournalEntry(
        string $id,
        string $status,
        string $date,
        string $total,
        ?string $deletedAt,
    ): void {
        $pdo = $this->requirePdo();
        $pdo->prepare(
            'INSERT INTO journal_entries (
                id, entity_id, fiscal_term_id, journal_date, booked_at, summary,
                total_amount, currency_code, status, source, created_by,
                created_at, updated_at, deleted_at
             ) VALUES (
                :id, :ent, :term, :dt, :bk, :sum,
                :tot, "JPY", :st, "manual", :cb,
                NOW(6), NOW(6), :del
             )'
        )->execute([
            ':id'   => UlidGenerator::decode($id),
            ':ent'  => UlidGenerator::decode($this->entityId),
            ':term' => UlidGenerator::decode($this->fiscalTermId),
            ':dt'   => $date,
            ':bk'   => $date . ' 12:00:00.000000',
            ':sum'  => 'seed',
            ':tot'  => $total,
            ':st'   => $status,
            ':cb'   => UlidGenerator::decode($this->userId),
            ':del'  => $deletedAt,
        ]);
    }

    private function insertJournalLine(
        string $entryId,
        int $lineNo,
        string $side,
        string $accountId,
        string $amount,
    ): void {
        $pdo = $this->requirePdo();
        $pdo->prepare(
            'INSERT INTO journal_entry_lines (
                id, entry_id, line_no, side, account_title_id, amount, booked_at, created_at, updated_at
             ) VALUES (
                :id, :entry, :ln, :side, :acc, :amt, NOW(6), NOW(6), NOW(6)
             )'
        )->execute([
            ':id'    => UlidGenerator::decode($this->ulids->generate()),
            ':entry' => UlidGenerator::decode($entryId),
            ':ln'    => $lineNo,
            ':side'  => $side,
            ':acc'   => UlidGenerator::decode($accountId),
            ':amt'   => $amount,
        ]);
    }

    private function requirePdo(): PDO
    {
        if ($this->pdo === null) {
            throw new \RuntimeException('PDO not initialised; integration test should have been skipped.');
        }
        return $this->pdo;
    }
}
