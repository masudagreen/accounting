<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Import\LegacyImport;

use PDO;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Import\LegacyImport\IdMapping;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * IdMapping uses a persistent store. Driving it through sqlite :memory:
 * gives us exact INT-id / ULID round-trip coverage without spinning up
 * MariaDB, while still hitting the real {@see PDO} code path.
 */
final class IdMappingTest extends TestCase
{
    private \PDO $pdo;

    #[\Override]
    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        // Replicate the MariaDB layout using sqlite-compatible types.
        $this->pdo->exec(
            'CREATE TABLE legacy_id_mapping (
                legacy_table TEXT NOT NULL,
                legacy_id    TEXT NOT NULL,
                new_ulid     BLOB NOT NULL,
                imported_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (legacy_table, legacy_id)
            )',
        );
    }

    public function testLookupReturnsNullForUnknownId(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());
        self::assertNull($map->lookup('accountingEntity', 999));
    }

    public function testGetOrCreatePersistsAndIsIdempotent(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());

        $first = $map->getOrCreate('accountingEntity', 1);
        $second = $map->getOrCreate('accountingEntity', 1);

        self::assertSame(16, strlen($first));
        self::assertSame($first, $second, 'same legacy id must return same ULID');
    }

    public function testRequireThrowsWhenMissing(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());

        $this->expectException(\RuntimeException::class);
        $map->require('baseAccount', 123);
    }

    public function testRequireReturnsExistingUlid(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());
        $created = $map->getOrCreate('baseAccount', 1);
        self::assertSame($created, $map->require('baseAccount', 1));
    }

    public function testStringAndIntLegacyIdsAreNormalisedConsistently(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());
        $fromInt = $map->getOrCreate('accountingEntityJpn', 1);
        $fromString = $map->getOrCreate('accountingEntityJpn', '1');
        self::assertSame($fromInt, $fromString, 'legacy_id must be coerced consistently');
    }

    public function testForgetTableClearsOnlyTargetRows(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());
        $keepUlid = $map->getOrCreate('baseAccount', 1);
        $map->getOrCreate('accountingEntity', 1);

        $map->forgetTable('accountingEntity');

        self::assertSame($keepUlid, $map->lookup('baseAccount', 1));
        self::assertNull($map->lookup('accountingEntity', 1));
    }

    public function testPersistRejectsWrongBinaryLength(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());
        $this->expectException(\RuntimeException::class);
        $map->persist('baseAccount', 1, 'too-short');
    }

    /**
     * Regression: `LegacyToV2Command::truncateTarget()` issued raw
     * `DELETE ... WHERE id IN (SELECT new_ulid FROM legacy_id_mapping)`
     * before the bookkeeping table was created, so the very first import
     * run blew up with `SQLSTATE[42S02]: Base table or view not found`
     * (see `plan/reality-check-report.md` RC-5).
     *
     * The fix is to call `IdMapping::bootstrapSchema()` eagerly. This
     * test pins down that the helper is idempotent and safe to call when
     * the table already exists (production re-run scenario), which the
     * test setUp simulates by pre-creating the table.
     *
     * Note: the production `ensureSchema()` DDL is MariaDB-specific
     * (`KEY ... ENGINE=InnoDB`). Verifying the fresh-DB path requires
     * MariaDB and is exercised in integration via `legacy:import
     * --truncate-target` against a freshly migrated target.
     */
    public function testBootstrapSchemaIsIdempotentWhenTableExists(): void
    {
        $map = new IdMapping($this->pdo, new UlidGenerator());

        $map->bootstrapSchema();
        $map->bootstrapSchema();

        $stmt = $this->pdo->query('SELECT COUNT(*) FROM legacy_id_mapping');
        self::assertNotFalse($stmt);
        self::assertSame(0, (int) $stmt->fetchColumn());
    }
}
