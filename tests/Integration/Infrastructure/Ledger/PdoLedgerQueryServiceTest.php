<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Infrastructure\Ledger;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Infrastructure\Ledger\PdoLedgerQueryService;
use Rucaro\Infrastructure\Migration\MigrationRunner;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Integration tests for the PDO-backed Ledger infrastructure.
 *
 * Requires env:
 *   RUCARO_TEST_DB_DSN   e.g. "mysql:host=127.0.0.1;port=3306"
 *   RUCARO_TEST_DB_USER
 *   RUCARO_TEST_DB_PASS  (may be empty)
 *   RUCARO_TEST_DB_NAME  (default rucaro_test)
 *
 * Skips cleanly when any of the required env vars is missing.
 */
#[CoversClass(PdoLedgerQueryService::class)]
final class PdoLedgerQueryServiceTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $dbName = '';
    private UlidGenerator $ulids;

    private string $entityId = '';
    private string $fiscalTermId = '';
    private string $cashAccountId = '';
    private string $salesAccountId = '';
    private string $bankAccountId = '';
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

    public function testSingleAccountQueryReturnsOneBookWithOrderedEntries(): void
    {
        $svc = new PdoLedgerQueryService($this->requirePdo());
        $ledger = $svc->query(
            $this->entityId,
            $this->fiscalTermId,
            $this->cashAccountId,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        self::assertCount(1, $ledger->books);
        $book = $ledger->books[0];
        self::assertSame('101', $book->accountTitleCode);
        self::assertCount(2, $book->entries);
        self::assertSame('2026-04-05', $book->entries[0]->entryDate->format('Y-m-d'));
        self::assertSame('2026-04-20', $book->entries[1]->entryDate->format('Y-m-d'));
        self::assertSame('5000.0000', $book->entries[0]->debitAmount);
        self::assertSame('401', $book->entries[0]->counterAccountCode);
    }

    public function testAllAccountsQueryReturnsBooksSortedByCode(): void
    {
        $svc = new PdoLedgerQueryService($this->requirePdo());
        $ledger = $svc->query(
            $this->entityId,
            $this->fiscalTermId,
            null,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        // Three accounts seeded (cash, bank, sales); bank has no entries.
        self::assertCount(3, $ledger->books);
        self::assertSame('101', $ledger->books[0]->accountTitleCode);
        self::assertSame('102', $ledger->books[1]->accountTitleCode);
        self::assertSame('401', $ledger->books[2]->accountTitleCode);
        self::assertSame([], $ledger->books[1]->entries);
    }

    public function testMultiLineEntryProducesSundriesCounter(): void
    {
        // Insert a 3-line entry: cash + bank debit, sales credit
        $pdo = $this->requirePdo();
        $entryId = $this->ulids->generate();
        $this->insertJournalEntry($entryId, 'posted', '2026-04-25', '10000.0000', null);
        $this->insertJournalLine($entryId, 1, 'debit',  $this->cashAccountId,  '3000.0000');
        $this->insertJournalLine($entryId, 2, 'debit',  $this->bankAccountId,  '7000.0000');
        $this->insertJournalLine($entryId, 3, 'credit', $this->salesAccountId, '10000.0000');

        $svc = new PdoLedgerQueryService($pdo);
        $ledger = $svc->query(
            $this->entityId,
            $this->fiscalTermId,
            $this->salesAccountId,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        $entries = $ledger->books[0]->entries;
        $mixed = end($entries);
        self::assertNotFalse($mixed);
        self::assertSame(LedgerEntry::COUNTER_SUNDRIES, $mixed->counterAccountName);
    }

    public function testDraftsAndDeletedEntriesAreExcluded(): void
    {
        $pdo = $this->requirePdo();
        // Draft entry should be ignored
        $draftId = $this->ulids->generate();
        $this->insertJournalEntry($draftId, 'draft', '2026-04-28', '999.0000', null);
        $this->insertJournalLine($draftId, 1, 'debit',  $this->cashAccountId,  '999.0000');
        $this->insertJournalLine($draftId, 2, 'credit', $this->salesAccountId, '999.0000');

        // Deleted entry should be ignored
        $deletedId = $this->ulids->generate();
        $this->insertJournalEntry($deletedId, 'posted', '2026-04-29', '777.0000', '2026-04-30 00:00:00.000000');
        $this->insertJournalLine($deletedId, 1, 'debit',  $this->cashAccountId,  '777.0000');
        $this->insertJournalLine($deletedId, 2, 'credit', $this->salesAccountId, '777.0000');

        $svc = new PdoLedgerQueryService($pdo);
        $ledger = $svc->query(
            $this->entityId,
            $this->fiscalTermId,
            $this->cashAccountId,
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-30'),
        );

        // Only the two seeded posted entries remain.
        self::assertCount(2, $ledger->books[0]->entries);
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
            ':em'  => 'ledger-test@example.com',
            ':emn' => 'ledger-test@example.com',
            ':pw'  => 'x',
            ':dn'  => 'Ledger test',
            ':role' => 'owner',
        ]);

        $this->entityId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO entities (id, owner_user_id, name, nation_code, currency_code, fiscal_start_mmdd, is_active, created_at, updated_at)
             VALUES (:id, :ow, :nm, "JPN", "JPY", "0401", 1, NOW(6), NOW(6))'
        )->execute([
            ':id' => UlidGenerator::decode($this->entityId),
            ':ow' => UlidGenerator::decode($this->userId),
            ':nm' => 'Ledger Entity',
        ]);

        $this->fiscalTermId = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date, is_closed, created_at, updated_at)
             VALUES (:id, :ent, 1, "2026-04-01", "2027-03-31", 0, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($this->fiscalTermId),
            ':ent' => UlidGenerator::decode($this->entityId),
        ]);

        $this->cashAccountId = $this->insertAccount('101', '現金', 'asset', 'debit', 1);
        $this->bankAccountId = $this->insertAccount('102', '当座預金', 'asset', 'debit', 2);
        $this->salesAccountId = $this->insertAccount('401', '売上', 'revenue', 'credit', 3);

        // Two posted entries — 5000 on Apr 5 and 3000 on Apr 20.
        $e1 = $this->ulids->generate();
        $this->insertJournalEntry($e1, 'posted', '2026-04-05', '5000.0000', null);
        $this->insertJournalLine($e1, 1, 'debit',  $this->cashAccountId,  '5000.0000');
        $this->insertJournalLine($e1, 2, 'credit', $this->salesAccountId, '5000.0000');

        $e2 = $this->ulids->generate();
        $this->insertJournalEntry($e2, 'posted', '2026-04-20', '3000.0000', null);
        $this->insertJournalLine($e2, 1, 'debit',  $this->cashAccountId,  '3000.0000');
        $this->insertJournalLine($e2, 2, 'credit', $this->salesAccountId, '3000.0000');
    }

    private function insertAccount(string $code, string $name, string $category, string $normal, int $sort): string
    {
        $pdo = $this->requirePdo();
        $id = $this->ulids->generate();
        $pdo->prepare(
            'INSERT INTO account_titles (id, entity_id, code, name, category, normal_side, sort_order, is_active, created_at, updated_at)
             VALUES (:id, :ent, :c, :n, :cat, :ns, :so, 1, NOW(6), NOW(6))'
        )->execute([
            ':id'  => UlidGenerator::decode($id),
            ':ent' => UlidGenerator::decode($this->entityId),
            ':c'   => $code,
            ':n'   => $name,
            ':cat' => $category,
            ':ns'  => $normal,
            ':so'  => $sort,
        ]);
        return $id;
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
            ':sum'  => 'ledger seed',
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
