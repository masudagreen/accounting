<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Import\LegacyImport;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Import\LegacyImport\IdMapping;
use Rucaro\Infrastructure\Import\LegacyImport\LegacyAccountTitleImporter;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Drives {@see LegacyAccountTitleImporter} with sqlite-backed source / target.
 *
 * F-3 expectation: the importer reads the master JSON tree from
 * `accountingFSJpn.jsonJgaapAccountTitle{BS,PL,CR}` and materialises every
 * leaf into `account_titles` per entity — not just the codes that show up
 * in `accountingLogCalcJpn`. Codes referenced by journals but absent from
 * the tree (legacy data hygiene gap) are still imported as a fallback so
 * journal FK lookups never break.
 */
#[CoversClass(LegacyAccountTitleImporter::class)]
final class LegacyAccountTitleImporterTest extends TestCase
{
    private \PDO $source;
    private \PDO $target;
    private IdMapping $idMap;

    #[\Override]
    protected function setUp(): void
    {
        $this->source = $this->newSqlite();
        $this->target = $this->newSqlite();

        // ---- source schema (subset of legacy) ----
        $this->source->exec(
            'CREATE TABLE accountingEntity (
                id INTEGER PRIMARY KEY AUTOINCREMENT
            )',
        );
        $this->source->exec(
            'CREATE TABLE accountingLogCalcJpn (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                idEntity        INTEGER NOT NULL,
                numFiscalPeriod INTEGER NOT NULL,
                idAccountTitle  TEXT
            )',
        );
        $this->source->exec(
            'CREATE TABLE accountingFSJpn (
                id                       INTEGER PRIMARY KEY AUTOINCREMENT,
                idEntity                 INTEGER NOT NULL,
                numFiscalPeriod          INTEGER NOT NULL,
                jsonJgaapAccountTitleBS  TEXT,
                jsonJgaapAccountTitlePL  TEXT,
                jsonJgaapAccountTitleCR  TEXT
            )',
        );

        // ---- target schema (subset of account_titles + id_mapping) ----
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
            'CREATE TABLE account_titles (
                id          BLOB NOT NULL,
                entity_id   BLOB NOT NULL,
                code        TEXT NOT NULL,
                name        TEXT NOT NULL,
                category    TEXT NOT NULL,
                normal_side TEXT NOT NULL,
                parent_id   BLOB,
                sort_order  INTEGER NOT NULL DEFAULT 0,
                is_active   INTEGER NOT NULL DEFAULT 1,
                PRIMARY KEY (id),
                UNIQUE (entity_id, code)
            )',
        );

        $this->idMap = new IdMapping($this->target, new UlidGenerator());
    }

    public function testImportsEveryLeafFromBsAndPlTrees(): void
    {
        $this->insertEntity(1);
        $this->insertFsTree(
            entity: 1,
            period: 1,
            bs: $this->minimalBsTree(),
            pl: $this->minimalPlTree(),
        );
        $this->mapEntity(1);

        $report = $this->newImporter()->run();

        $rows = $this->target->query(
            'SELECT code, name, category, normal_side, sort_order
               FROM account_titles
              ORDER BY sort_order',
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Tree leaves: cash, ordinaryDeposit (BS), netSales, rents (PL).
        // Subtotals (currentAssetsSum, assetsSum, salesSum, ...) excluded.
        $codes = array_column($rows, 'name');
        $hasJapaneseLabel = static function (string $jp) use ($codes): bool {
            foreach ($codes as $name) {
                if (str_contains($name, $jp)) {
                    return true;
                }
            }

            return false;
        };
        self::assertTrue($hasJapaneseLabel('現金'));
        self::assertTrue($hasJapaneseLabel('普通預金'));
        self::assertTrue($hasJapaneseLabel('売上高'));
        self::assertTrue($hasJapaneseLabel('地代家賃'));

        // Subtotal labels must NOT appear.
        self::assertFalse($hasJapaneseLabel('資産の部合計'));
        self::assertFalse($hasJapaneseLabel('売上総利益'));

        self::assertSame(4, count($rows));
        self::assertSame('account_titles', $report->stage);
        self::assertSame(4, $report->inserted);
    }

    public function testImportsCodesReferencedByJournalsButMissingFromTree(): void
    {
        // Tree has only `cash`. Journal references `mysteryCode` too.
        $tree = [[
            'vars' => ['idTarget' => 'assets', 'flagDebit' => 1],
            'strTitle' => '資産',
            'child' => [
                ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
            ],
        ]];
        $this->insertEntity(1);
        $this->insertFsTree(1, 1, $tree, []);
        $this->insertJournalLine(1, 'cash');
        $this->insertJournalLine(1, 'mysteryCode');
        $this->mapEntity(1);

        $this->newImporter()->run();

        $codes = $this->target
            ->query('SELECT name FROM account_titles ORDER BY sort_order')
            ->fetchAll(\PDO::FETCH_COLUMN);

        // Both should be imported; mysteryCode falls back to expense/debit
        // and uses the legacy code as label suffix.
        $joined = implode('|', $codes);
        self::assertStringContainsString('現金', $joined);
        self::assertStringContainsString('mysteryCode', $joined);
    }

    public function testHandlesMultipleEntitiesIndependently(): void
    {
        $this->insertEntity(1);
        $this->insertEntity(2);

        // Entity 1 has cash + accountsReceivable.
        $this->insertFsTree(1, 1, [[
            'vars' => ['idTarget' => 'assets', 'flagDebit' => 1],
            'strTitle' => '資産',
            'child' => [
                ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
                ['vars' => ['idTarget' => 'accountsReceivable', 'flagDebit' => 1], 'strTitle' => '売掛金'],
            ],
        ]], []);

        // Entity 2 has cash only.
        $this->insertFsTree(2, 1, [[
            'vars' => ['idTarget' => 'assets', 'flagDebit' => 1],
            'strTitle' => '資産',
            'child' => [
                ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
            ],
        ]], []);

        $this->mapEntity(1);
        $this->mapEntity(2);

        $this->newImporter()->run();

        $perEntity = $this->target
            ->query('SELECT entity_id, COUNT(*) AS n FROM account_titles GROUP BY entity_id')
            ->fetchAll(\PDO::FETCH_ASSOC);
        $counts = array_column($perEntity, 'n');
        sort($counts);
        self::assertSame([1, 2], array_map('intval', $counts));
    }

    public function testUsesMostRecentPeriodWhenSameEntityHasMultipleSnapshots(): void
    {
        // Tree at period 1 has cash only; period 2 adds accountsReceivable.
        // Importer should use the LATEST period's tree.
        $treeP1 = [[
            'vars' => ['idTarget' => 'assets', 'flagDebit' => 1], 'strTitle' => '資産',
            'child' => [
                ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
            ],
        ]];
        $treeP2 = [[
            'vars' => ['idTarget' => 'assets', 'flagDebit' => 1], 'strTitle' => '資産',
            'child' => [
                ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
                ['vars' => ['idTarget' => 'accountsReceivable', 'flagDebit' => 1], 'strTitle' => '売掛金'],
            ],
        ]];
        $this->insertEntity(1);
        $this->insertFsTree(1, 1, $treeP1, []);
        $this->insertFsTree(1, 2, $treeP2, []);
        $this->mapEntity(1);

        $this->newImporter()->run();

        $names = $this->target
            ->query('SELECT name FROM account_titles ORDER BY sort_order')
            ->fetchAll(\PDO::FETCH_COLUMN);
        $joined = implode('|', $names);
        self::assertStringContainsString('現金', $joined);
        self::assertStringContainsString('売掛金', $joined);
    }

    // -----------------------------------------------------------------
    // helpers
    // -----------------------------------------------------------------

    private function newImporter(): LegacyAccountTitleImporter
    {
        return new LegacyAccountTitleImporter(
            $this->source,
            $this->target,
            $this->idMap,
            dryRun: false,
        );
    }

    private function newSqlite(): \PDO
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    private function insertEntity(int $id): void
    {
        $this->source->prepare('INSERT INTO accountingEntity (id) VALUES (:id)')
            ->execute([':id' => $id]);
    }

    /**
     * @param list<array<string,mixed>> $bs
     * @param list<array<string,mixed>> $pl
     */
    private function insertFsTree(int $entity, int $period, array $bs, array $pl): void
    {
        $stmt = $this->source->prepare(
            'INSERT INTO accountingFSJpn
                 (idEntity, numFiscalPeriod,
                  jsonJgaapAccountTitleBS, jsonJgaapAccountTitlePL,
                  jsonJgaapAccountTitleCR)
             VALUES (:e, :p, :bs, :pl, NULL)',
        );
        $stmt->execute([
            ':e' => $entity,
            ':p' => $period,
            ':bs' => $bs === [] ? null : json_encode($bs, \JSON_UNESCAPED_UNICODE),
            ':pl' => $pl === [] ? null : json_encode($pl, \JSON_UNESCAPED_UNICODE),
        ]);
    }

    private function insertJournalLine(int $entity, string $code): void
    {
        $this->source->prepare(
            'INSERT INTO accountingLogCalcJpn (idEntity, numFiscalPeriod, idAccountTitle)
             VALUES (:e, 1, :c)',
        )->execute([':e' => $entity, ':c' => $code]);
    }

    private function mapEntity(int $legacyId): void
    {
        $this->idMap->getOrCreate(IdMapping::TABLE_ENTITIES, $legacyId);
    }

    /** @return list<array<string,mixed>> */
    private function minimalBsTree(): array
    {
        return [
            [
                'vars' => ['idTarget' => 'assets', 'flagDebit' => 1],
                'strTitle' => '資産',
                'child' => [
                    [
                        'vars' => ['idTarget' => 'currentAssets', 'flagDebit' => 1],
                        'strTitle' => '流動資産',
                        'child' => [
                            ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
                            ['vars' => ['idTarget' => 'ordinaryDeposit', 'flagDebit' => 1], 'strTitle' => '普通預金'],
                            ['vars' => ['idTarget' => 'currentAssetsSum', 'flagDebit' => 1], 'strTitle' => '流動資産合計'],
                        ],
                    ],
                ],
            ],
            ['vars' => ['idTarget' => 'assetsSum', 'flagDebit' => 1], 'strTitle' => '資産の部合計'],
        ];
    }

    /** @return list<array<string,mixed>> */
    private function minimalPlTree(): array
    {
        return [
            [
                'vars' => ['idTarget' => 'sales', 'flagDebit' => 0],
                'strTitle' => '売上高',
                'child' => [
                    ['vars' => ['idTarget' => 'netSales', 'flagDebit' => 0], 'strTitle' => '売上高'],
                    ['vars' => ['idTarget' => 'salesSum', 'flagDebit' => 0], 'strTitle' => '売上高合計'],
                ],
            ],
            ['vars' => ['idTarget' => 'salesSum', 'flagDebit' => 0], 'strTitle' => '売上高合計'],
            ['vars' => ['idTarget' => 'grossProfitOrLossNet', 'flagDebit' => 0], 'strTitle' => '売上総利益'],
            [
                'vars' => ['idTarget' => 'sellingGeneralAndAdministrationExpenses', 'flagDebit' => 1],
                'strTitle' => '販管費',
                'child' => [
                    ['vars' => ['idTarget' => 'rents', 'flagDebit' => 1], 'strTitle' => '地代家賃'],
                ],
            ],
        ];
    }
}
