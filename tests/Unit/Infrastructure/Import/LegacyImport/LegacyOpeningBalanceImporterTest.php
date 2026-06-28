<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Import\LegacyImport;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Import\LegacyImport\IdMapping;
use Rucaro\Infrastructure\Import\LegacyImport\LegacyOpeningBalanceImporter;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Drives {@see LegacyOpeningBalanceImporter} with sqlite-backed `source` and
 * `target` PDOs. Exercises the real SQL aggregation against the `accountingLogCalcJpn`
 * schema so the SUM / WHERE shape stays in sync with master.
 *
 * Phase B-3c: this importer materialises 期首繰越 by aggregating
 * `accountingLogCalcJpn` per (entity, period, account_title) for every
 * earlier period. Asset / liability / equity rows only — PL accounts
 * intentionally skipped (their carry happens via P&L → 利益剰余金 at close).
 */
#[CoversClass(LegacyOpeningBalanceImporter::class)]
final class LegacyOpeningBalanceImporterTest extends TestCase
{
    private \PDO $source;
    private \PDO $target;
    private IdMapping $idMap;

    #[\Override]
    protected function setUp(): void
    {
        $this->source = $this->newSqlite();
        $this->target = $this->newSqlite();

        // ---- source schema (subset of legacy accountingLogCalcJpn) ----
        $this->source->exec(
            'CREATE TABLE accountingLogCalcJpn (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                idEntity        INTEGER NOT NULL,
                numFiscalPeriod INTEGER NOT NULL,
                idAccountTitle  TEXT,
                flagDebit       INTEGER,
                numValue        INTEGER
            )',
        );
        // ---- target schema (subset of opening_balances + id_mapping) ----
        $this->target->exec(
            'CREATE TABLE legacy_id_mapping (
                legacy_table TEXT NOT NULL,
                legacy_id    TEXT NOT NULL,
                new_ulid     BLOB NOT NULL,
                imported_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (legacy_table, legacy_id)
            )',
        );
        $this->target->exec(
            'CREATE TABLE opening_balances (
                id                BLOB NOT NULL,
                entity_id         BLOB NOT NULL,
                fiscal_term_id    BLOB NOT NULL,
                account_title_id  BLOB NOT NULL,
                amount            TEXT NOT NULL DEFAULT "0.0000",
                currency_code     TEXT NOT NULL DEFAULT "JPY",
                created_at        TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at        TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE (entity_id, fiscal_term_id, account_title_id)
            )',
        );

        $this->idMap = new IdMapping($this->target, new UlidGenerator());
    }

    public function testProducesNoOpeningRowsForFirstPeriod(): void
    {
        // Only period 14 exists; opening for the earliest term is by
        // definition zero, so nothing should be inserted.
        $this->seedLine(entity: 1, period: 14, code: 'cash', flagDebit: 1, value: 1000);
        $this->mapEntity(1);
        $this->mapFiscalTerm(1, 14);
        $this->mapAccount(1, 'cash');

        $report = $this->newImporter()->run();

        $count = $this->target->query('SELECT COUNT(*) FROM opening_balances')?->fetchColumn();
        self::assertSame(0, (int) $count, 'first-period opening must be zero (no row written)');
        self::assertSame('opening_balances', $report->stage);
        self::assertGreaterThanOrEqual(0, $report->read);
    }

    public function testAggregatesPriorPeriodsForBalanceSheetAccount(): void
    {
        // Reproduces RC-8: cash dr - cr summed across periods 14..19 should
        // be 3,897,237 and become the period-20 opening.
        $this->seedLine(1, 14, 'cash', 1, 5_000_000); // dr
        $this->seedLine(1, 14, 'cash', 0, 1_000_000); // cr
        $this->seedLine(1, 15, 'cash', 1, 2_000_000);
        $this->seedLine(1, 15, 'cash', 0, 500_000);
        $this->seedLine(1, 16, 'cash', 1, 1_000_000);
        $this->seedLine(1, 16, 'cash', 0, 200_000);
        $this->seedLine(1, 17, 'cash', 1, 1_500_000);
        $this->seedLine(1, 17, 'cash', 0, 1_300_000);
        $this->seedLine(1, 18, 'cash', 1, 800_000);
        $this->seedLine(1, 18, 'cash', 0, 1_400_000);
        $this->seedLine(1, 19, 'cash', 1, 500_000);
        $this->seedLine(1, 19, 'cash', 0, 2_502_763);
        // Plus a period-20 line — must NOT be folded into period-20 opening
        $this->seedLine(1, 20, 'cash', 1, 9_000_000);
        // Cumulative dr - cr through period 19 = 10_800_000 - 6_902_763 = 3_897_237

        $this->mapEntity(1);
        foreach ([14, 15, 16, 17, 18, 19, 20] as $p) {
            $this->mapFiscalTerm(1, $p);
        }
        $this->mapAccount(1, 'cash');

        $this->newImporter()->run();

        $row = $this->fetchOpening(1, 20, 'cash');
        self::assertNotNull($row, 'period-20 opening row missing');
        self::assertSame(3_897_237.0, (float) $row['amount']);

        // Period 14 itself never gets a row (no prior periods exist).
        self::assertNull($this->fetchOpening(1, 14, 'cash'));
    }

    public function testSkipsProfitAndLossAccounts(): void
    {
        // netSales is a revenue account → no opening row even if it has
        // significant prior-period activity.
        $this->seedLine(1, 14, 'netSales', 0, 500_000);
        $this->seedLine(1, 15, 'netSales', 0, 700_000);
        $this->mapEntity(1);
        $this->mapFiscalTerm(1, 14);
        $this->mapFiscalTerm(1, 15);
        $this->mapAccount(1, 'netSales');

        $this->newImporter()->run();

        self::assertNull($this->fetchOpening(1, 15, 'netSales'));
    }

    public function testWritesSeparateOpeningsPerEntity(): void
    {
        $this->seedLine(1, 1, 'cash', 1, 100);
        $this->seedLine(1, 2, 'cash', 1, 1); // ensure period 2 exists for entity 1
        $this->seedLine(2, 1, 'cash', 1, 200);
        $this->seedLine(2, 2, 'cash', 1, 1);

        $this->mapEntity(1);
        $this->mapEntity(2);
        $this->mapFiscalTerm(1, 1);
        $this->mapFiscalTerm(1, 2);
        $this->mapFiscalTerm(2, 1);
        $this->mapFiscalTerm(2, 2);
        $this->mapAccount(1, 'cash');
        $this->mapAccount(2, 'cash');

        $this->newImporter()->run();

        self::assertSame(100.0, (float) ($this->fetchOpening(1, 2, 'cash')['amount'] ?? -1));
        self::assertSame(200.0, (float) ($this->fetchOpening(2, 2, 'cash')['amount'] ?? -1));
    }

    public function testIsIdempotentOnReRun(): void
    {
        $this->seedLine(1, 14, 'cash', 1, 1_000);
        $this->seedLine(1, 15, 'cash', 1, 1);
        $this->mapEntity(1);
        $this->mapFiscalTerm(1, 14);
        $this->mapFiscalTerm(1, 15);
        $this->mapAccount(1, 'cash');

        $this->newImporter()->run();
        $this->newImporter()->run();

        $count = (int) ($this->target->query('SELECT COUNT(*) FROM opening_balances')?->fetchColumn() ?: 0);
        self::assertSame(1, $count, 'a re-run must not create duplicate rows');

        $row = $this->fetchOpening(1, 15, 'cash');
        self::assertNotNull($row);
        self::assertSame(1_000.0, (float) $row['amount']);
    }

    public function testSkipsZeroOpenings(): void
    {
        // dr 100 / cr 100 → net 0 → no row.
        $this->seedLine(1, 14, 'cash', 1, 100);
        $this->seedLine(1, 14, 'cash', 0, 100);
        $this->seedLine(1, 15, 'cash', 1, 1);
        $this->mapEntity(1);
        $this->mapFiscalTerm(1, 14);
        $this->mapFiscalTerm(1, 15);
        $this->mapAccount(1, 'cash');

        $this->newImporter()->run();

        self::assertNull($this->fetchOpening(1, 15, 'cash'), 'zero opening must be omitted');
    }

    public function testHandlesNegativeOpeningForCreditNormalAccount(): void
    {
        // accounts payable (liability, credit-normal). Master stores raw
        // dr / cr; convention is opening = SUM(cr - dr) for credit-normal,
        // SUM(dr - cr) for debit-normal. The importer must write the value
        // so the use case can fold it correctly.
        $this->seedLine(1, 14, 'accruedExpenses', 0, 5_000); // cr
        $this->seedLine(1, 14, 'accruedExpenses', 1, 1_500); // dr
        $this->seedLine(1, 15, 'accruedExpenses', 1, 1);
        $this->mapEntity(1);
        $this->mapFiscalTerm(1, 14);
        $this->mapFiscalTerm(1, 15);
        $this->mapAccount(1, 'accruedExpenses');

        $this->newImporter()->run();

        $row = $this->fetchOpening(1, 15, 'accruedExpenses');
        self::assertNotNull($row);
        // credit-normal: opening = cr - dr = 5000 - 1500 = 3500
        self::assertSame(3_500.0, (float) $row['amount']);
    }

    // -----------------------------------------------------------------
    // helpers
    // -----------------------------------------------------------------

    private function newImporter(): LegacyOpeningBalanceImporter
    {
        return new LegacyOpeningBalanceImporter(
            $this->source,
            $this->target,
            $this->idMap,
            new UlidGenerator(),
            dryRun: false,
        );
    }

    private function newSqlite(): \PDO
    {
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }

    private function seedLine(int $entity, int $period, string $code, int $flagDebit, int $value): void
    {
        $stmt = $this->source->prepare(
            'INSERT INTO accountingLogCalcJpn
                 (idEntity, numFiscalPeriod, idAccountTitle, flagDebit, numValue)
             VALUES (:e, :p, :a, :d, :v)',
        );
        $stmt->execute([
            ':e' => $entity,
            ':p' => $period,
            ':a' => $code,
            ':d' => $flagDebit,
            ':v' => $value,
        ]);
    }

    private function mapEntity(int $legacyId): string
    {
        return $this->idMap->getOrCreate(IdMapping::TABLE_ENTITIES, $legacyId);
    }

    private function mapFiscalTerm(int $entity, int $period): string
    {
        return $this->idMap->getOrCreate(
            IdMapping::TABLE_FISCAL_TERMS,
            sprintf('%d-%d', $entity, $period),
        );
    }

    private function mapAccount(int $entity, string $code): string
    {
        return $this->idMap->getOrCreate(
            IdMapping::TABLE_ACCOUNT_TITLES,
            sprintf('%d:%s', $entity, $code),
        );
    }

    /**
     * @return array<string,mixed>|null
     */
    private function fetchOpening(int $entity, int $period, string $code): ?array
    {
        $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entity);
        $termBin = $this->idMap->lookup(
            IdMapping::TABLE_FISCAL_TERMS,
            sprintf('%d-%d', $entity, $period),
        );
        $atBin = $this->idMap->lookup(
            IdMapping::TABLE_ACCOUNT_TITLES,
            sprintf('%d:%s', $entity, $code),
        );
        if ($entityBin === null || $termBin === null || $atBin === null) {
            return null;
        }

        $stmt = $this->target->prepare(
            'SELECT amount FROM opening_balances
              WHERE entity_id = :e AND fiscal_term_id = :t AND account_title_id = :a',
        );
        $stmt->bindValue(':e', $entityBin, \PDO::PARAM_LOB);
        $stmt->bindValue(':t', $termBin, \PDO::PARAM_LOB);
        $stmt->bindValue(':a', $atBin, \PDO::PARAM_LOB);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }
}
