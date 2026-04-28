<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use PDO;

/**
 * Discovers and executes file-based migrations.
 *
 * Convention:
 *   migrations/NNNN_description.up.sql    — forward migration
 *   migrations/NNNN_description.down.sql  — rollback migration
 *
 * NNNN is a zero-padded 4-digit version number (e.g. 0001, 0042).
 */
final class MigrationRunner
{
    public function __construct(
        private readonly MigrationRecordInterface $record,
        private readonly string $migrationsDir,
    ) {
    }

    // -------------------------------------------------------------------------
    // Version helpers
    // -------------------------------------------------------------------------

    /**
     * Parses the 4-digit version prefix from a migration filename.
     * Returns null when the filename does not match the convention.
     */
    public function parseVersion(string $filename): ?string
    {
        if (!preg_match('/^(\d{4})_/', basename($filename), $m)) {
            return null;
        }

        return $m[1];
    }

    // -------------------------------------------------------------------------
    // Discovery
    // -------------------------------------------------------------------------

    /**
     * Returns all UP migration files keyed by version, sorted ascending.
     *
     * @return array<string, string>  version => absolute path
     */
    public function discoverUpMigrations(): array
    {
        return $this->discoverByDirection('up');
    }

    /**
     * Returns all DOWN migration files keyed by version, sorted descending.
     *
     * @return array<string, string>  version => absolute path
     */
    public function discoverDownMigrations(): array
    {
        return $this->discoverByDirection('down', ascending: false);
    }

    /**
     * @return array<string, string>
     */
    private function discoverByDirection(string $direction, bool $ascending = true): array
    {
        $files = glob($this->migrationsDir . '/*.' . $direction . '.sql');

        if ($files === false) {
            return [];
        }

        $result = [];
        foreach ($files as $path) {
            $version = $this->parseVersion(basename($path));
            if ($version === null) {
                continue;
            }
            $result[$version] = $path;
        }

        if ($ascending) {
            ksort($result);
        } else {
            krsort($result);
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Pending / applied queries
    // -------------------------------------------------------------------------

    /**
     * Returns all UP migrations not yet applied, in ascending order.
     *
     * @return array<string, string>
     */
    public function getPending(): array
    {
        $applied = array_flip($this->record->getAppliedVersions());
        $result = [];

        foreach ($this->discoverUpMigrations() as $version => $path) {
            if (!isset($applied[$version])) {
                $result[$version] = $path;
            }
        }

        return $result;
    }

    /**
     * Returns pending UP migrations up to and including $target version.
     *
     * @return array<string, string>
     */
    public function getPendingUpTo(string $target): array
    {
        $result = [];
        foreach ($this->getPending() as $version => $path) {
            if ($version <= $target) {
                $result[$version] = $path;
            }
        }

        return $result;
    }

    /**
     * Returns applied DOWN migration files for versions strictly above $target,
     * sorted descending (so rollbacks happen in reverse order).
     *
     * @return array<string, string>
     * @throws MigrationException when a down file is missing for an applied version
     */
    public function getAppliedDownTo(string $target): array
    {
        $applied = array_flip($this->record->getAppliedVersions());
        $downFiles = $this->discoverDownMigrations();

        // Collect versions that are applied AND strictly above target
        $toRollback = [];
        foreach ($applied as $version => $_) {
            if ($version > $target) {
                if (!isset($downFiles[$version])) {
                    throw new MigrationException(
                        "Down migration file missing for applied version $version"
                    );
                }
                $toRollback[$version] = $downFiles[$version];
            }
        }

        krsort($toRollback);

        return $toRollback;
    }

    // -------------------------------------------------------------------------
    // Status report
    // -------------------------------------------------------------------------

    /**
     * Returns a map of version => 'applied'|'pending' for all known migrations.
     *
     * @return array<string, string>
     */
    public function getStatus(): array
    {
        $applied = array_flip($this->record->getAppliedVersions());
        $all = $this->discoverUpMigrations();

        $status = [];
        foreach ($all as $version => $_) {
            $status[$version] = isset($applied[$version]) ? 'applied' : 'pending';
        }

        return $status;
    }

    // -------------------------------------------------------------------------
    // Execution
    // -------------------------------------------------------------------------

    /**
     * Runs all pending UP migrations.
     */
    public function runUp(PDO $pdo): void
    {
        foreach ($this->getPending() as $version => $path) {
            $this->executeSqlFile($pdo, $path);
            $this->record->markApplied($version);
        }
    }

    /**
     * Runs pending UP migrations up to and including $target.
     */
    public function runUpTo(PDO $pdo, string $target): void
    {
        foreach ($this->getPendingUpTo($target) as $version => $path) {
            $this->executeSqlFile($pdo, $path);
            $this->record->markApplied($version);
        }
    }

    /**
     * Rolls back all applied migrations down to (but not including) $target.
     */
    public function runDown(PDO $pdo): void
    {
        $downFiles = $this->discoverDownMigrations();
        $applied = array_flip($this->record->getAppliedVersions());

        foreach ($downFiles as $version => $path) {
            if (isset($applied[$version])) {
                $this->executeSqlFile($pdo, $path);
                $this->record->markRolledBack($version);
            }
        }
    }

    /**
     * Rolls back applied migrations strictly above $target, in descending order.
     */
    public function runDownTo(PDO $pdo, string $target): void
    {
        foreach ($this->getAppliedDownTo($target) as $version => $path) {
            $this->executeSqlFile($pdo, $path);
            $this->record->markRolledBack($version);
        }
    }

    // -------------------------------------------------------------------------
    // File-naming helper
    // -------------------------------------------------------------------------

    /**
     * Suggests the base filename for a new migration (without .up.sql suffix).
     * Increments the highest existing version by 1.
     */
    public function buildNewFilename(string $description): string
    {
        $existing = $this->discoverUpMigrations();
        $next = empty($existing) ? 1 : (int) max(array_keys($existing)) + 1;

        return sprintf('%04d_%s', $next, $description);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private function executeSqlFile(PDO $pdo, string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new MigrationException("Cannot read migration file: $path");
        }

        // Split on semicolons, filter blank statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            static fn(string $s): bool => $s !== '',
        );

        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
    }
}
