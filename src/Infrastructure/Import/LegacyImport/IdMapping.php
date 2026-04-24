<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use RuntimeException;

/**
 * In-memory + DB-persisted legacy INT id <-> new ULID BINARY(16) mapping.
 *
 * Why:
 *   - Legacy rows key on auto-increment INT/BIGINT.
 *   - New schema uses ULID BINARY(16).
 *   - Importers run in multiple stages; downstream stages need to resolve
 *     upstream stage IDs (e.g. journals reference entities, fiscal_terms,
 *     account_titles).
 *   - Persisting the mapping lets idempotent re-runs (and rollback)
 *     know which legacy rows were already imported.
 *
 * The underlying `legacy_id_mapping` table is created on demand.
 */
final class IdMapping
{
    public const TABLE_USERS = 'baseAccount';
    public const TABLE_ENTITIES = 'accountingEntity';
    public const TABLE_FISCAL_TERMS = 'accountingEntityJpn';
    public const TABLE_ACCOUNT_TITLES = 'accountTitle';
    public const TABLE_SUB_ACCOUNT_TITLES = 'accountingSubAccountTitleJpn';
    public const TABLE_JOURNAL_ENTRIES = 'accountingLog';
    public const TABLE_FIXED_ASSETS = 'accountingLogFixedAssetsJpn';

    /**
     * Cache keyed by "<legacy_table>:<legacy_id>" -> binary ULID (16 bytes).
     *
     * @var array<string, string>
     */
    private array $cache = [];

    private bool $schemaEnsured = false;

    /**
     * @param bool $inMemoryOnly When true, no row is ever written to
     *   `legacy_id_mapping`. Used during `--dry-run` so previews don't
     *   leave artefacts in the target DB.
     */
    public function __construct(
        private readonly PDO $target,
        private readonly UlidGenerator $ulids,
        private readonly bool $inMemoryOnly = false,
    ) {
    }

    /**
     * Create the bookkeeping table eagerly. Useful before opening a
     * transaction — MariaDB's implicit commit on DDL would otherwise break
     * stage rollback.
     */
    public function bootstrapSchema(): void
    {
        if ($this->inMemoryOnly) {
            return;
        }
        $this->ensureSchema();
    }

    /**
     * Look up an existing mapping. Returns the binary ULID (16 bytes)
     * or null if not present.
     */
    public function lookup(string $legacyTable, int|string $legacyId): ?string
    {
        $key = $this->cacheKey($legacyTable, $legacyId);
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        if ($this->inMemoryOnly) {
            return null;
        }

        $this->ensureSchema();

        $stmt = $this->target->prepare(
            'SELECT new_ulid FROM legacy_id_mapping
              WHERE legacy_table = :t AND legacy_id = :i LIMIT 1'
        );
        $stmt->execute([':t' => $legacyTable, ':i' => (string) $legacyId]);
        /** @var string|false $bin */
        $bin = $stmt->fetchColumn();
        if ($bin === false) {
            return null;
        }
        $this->cache[$key] = (string) $bin;
        return (string) $bin;
    }

    /**
     * Look up or generate + persist a new ULID for the given legacy id.
     */
    public function getOrCreate(string $legacyTable, int|string $legacyId): string
    {
        $found = $this->lookup($legacyTable, $legacyId);
        if ($found !== null) {
            return $found;
        }
        $binary = $this->ulids->binary();
        $this->persist($legacyTable, $legacyId, $binary);
        return $binary;
    }

    /**
     * Require an existing mapping; throw if not found. Use for FK resolution
     * where the upstream stage must have run first.
     */
    public function require(string $legacyTable, int|string $legacyId): string
    {
        $bin = $this->lookup($legacyTable, $legacyId);
        if ($bin === null) {
            throw new RuntimeException(sprintf(
                'IdMapping: no mapping for %s#%s. Run the upstream stage first.',
                $legacyTable,
                (string) $legacyId,
            ));
        }
        return $bin;
    }

    /**
     * Persist a mapping. Idempotent: a duplicate (legacy_table, legacy_id)
     * pair is silently ignored. We avoid MySQL's non-portable
     * `INSERT IGNORE` so the same code drives both MariaDB (production) and
     * sqlite (unit tests).
     */
    public function persist(string $legacyTable, int|string $legacyId, string $binaryUlid): void
    {
        if (strlen($binaryUlid) !== 16) {
            throw new RuntimeException('IdMapping: new_ulid must be exactly 16 bytes');
        }

        $key = $this->cacheKey($legacyTable, $legacyId);

        if ($this->inMemoryOnly) {
            $this->cache[$key] = $binaryUlid;
            return;
        }

        $this->ensureSchema();

        // Cheap existence check keeps us portable across drivers.
        if ($this->lookup($legacyTable, $legacyId) !== null) {
            return;
        }

        $stmt = $this->target->prepare(
            'INSERT INTO legacy_id_mapping (legacy_table, legacy_id, new_ulid)
             VALUES (:t, :i, :u)'
        );
        $stmt->bindValue(':t', $legacyTable);
        $stmt->bindValue(':i', (string) $legacyId);
        $stmt->bindValue(':u', $binaryUlid, PDO::PARAM_LOB);
        $stmt->execute();

        $this->cache[$key] = $binaryUlid;
    }

    /**
     * Truncate all mappings (used by --truncate-target before re-import).
     */
    public function truncate(): void
    {
        $this->ensureSchema();
        $this->target->exec('DELETE FROM legacy_id_mapping');
        $this->cache = [];
    }

    /**
     * Delete rows matching the legacy_table prefix; keeps others untouched.
     */
    public function forgetTable(string $legacyTable): void
    {
        $this->ensureSchema();
        $stmt = $this->target->prepare('DELETE FROM legacy_id_mapping WHERE legacy_table = :t');
        $stmt->execute([':t' => $legacyTable]);
        foreach (array_keys($this->cache) as $k) {
            if (str_starts_with($k, $legacyTable . ':')) {
                unset($this->cache[$k]);
            }
        }
    }

    /**
     * Iterate every mapping known for a given legacy table.
     * Returns an array of `[legacyId => binaryUlid]`.
     *
     * Combines in-memory cache + DB rows so callers see a consistent view
     * regardless of whether we are in dry-run mode.
     *
     * @return array<string, string>
     */
    public function allFor(string $legacyTable): array
    {
        /** @var array<string, string> $out */
        $out = [];

        // Cache first — covers dry-run-only data.
        foreach ($this->cache as $k => $bin) {
            if (str_starts_with($k, $legacyTable . ':')) {
                $out[substr($k, strlen($legacyTable) + 1)] = $bin;
            }
        }

        if ($this->inMemoryOnly) {
            return $out;
        }

        $this->ensureSchema();
        $stmt = $this->target->prepare(
            'SELECT legacy_id, new_ulid FROM legacy_id_mapping WHERE legacy_table = :t'
        );
        $stmt->execute([':t' => $legacyTable]);
        foreach ($stmt as $row) {
            /** @var array<string,mixed> $row */
            $id = (string) $row['legacy_id'];
            if (!isset($out[$id])) {
                $out[$id] = (string) $row['new_ulid'];
            }
        }
        return $out;
    }

    private function ensureSchema(): void
    {
        if ($this->schemaEnsured) {
            return;
        }

        // If the table already exists (tests pre-create it in sqlite;
        // production re-runs see it from prior invocations) we skip DDL.
        if ($this->tableExists('legacy_id_mapping')) {
            $this->schemaEnsured = true;
            return;
        }

        $this->target->exec(
            'CREATE TABLE IF NOT EXISTS legacy_id_mapping (
                legacy_table VARCHAR(64) NOT NULL,
                legacy_id    VARCHAR(64) NOT NULL,
                new_ulid     BINARY(16)  NOT NULL,
                imported_at  TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
                PRIMARY KEY (legacy_table, legacy_id),
                KEY idx_lim__ulid (new_ulid)
            ) ENGINE=InnoDB
              DEFAULT CHARACTER SET utf8mb4
              COLLATE utf8mb4_unicode_ci
              COMMENT="Legacy INT id -> new ULID mapping (migration artefact)"'
        );
        $this->schemaEnsured = true;
    }

    private function tableExists(string $table): bool
    {
        try {
            $stmt = $this->target->query(sprintf('SELECT 1 FROM %s LIMIT 1', $table));
            if ($stmt !== false) {
                $stmt->closeCursor();
                return true;
            }
        } catch (\PDOException) {
            return false;
        }
        return false;
    }

    private function cacheKey(string $legacyTable, int|string $legacyId): string
    {
        return $legacyTable . ':' . (string) $legacyId;
    }
}
