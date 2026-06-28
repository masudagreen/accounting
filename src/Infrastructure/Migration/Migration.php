<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Migration;

/**
 * Immutable DTO describing a single migration pair on disk.
 *
 * A migration is identified by a version string (zero-padded numeric prefix
 * of the file, e.g. "0001"). The up-SQL path is always present. The down-SQL
 * path is optional; if absent, rollback is not supported for this migration.
 *
 * A companion `*_seed.sql` file at the same version, if present, is run
 * after the up-SQL within the same apply step and is part of the same
 * logical migration. Its content is included in the checksum so that
 * silent rewrites are detected.
 */
final readonly class Migration
{
    public function __construct(
        public string $version,
        public string $name,
        public string $upPath,
        public ?string $downPath,
        public ?string $seedPath = null,
    ) {
    }

    public function hasDown(): bool
    {
        return $this->downPath !== null;
    }

    public function hasSeed(): bool
    {
        return $this->seedPath !== null;
    }

    public function readUpSql(): string
    {
        $sql = @file_get_contents($this->upPath);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Failed to read migration file: %s', $this->upPath));
        }

        return $sql;
    }

    public function readSeedSql(): string
    {
        if ($this->seedPath === null) {
            throw new \RuntimeException(sprintf('Migration %s has no seed file', $this->version));
        }
        $sql = @file_get_contents($this->seedPath);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Failed to read seed file: %s', $this->seedPath));
        }

        return $sql;
    }

    public function readDownSql(): string
    {
        if ($this->downPath === null) {
            throw new \RuntimeException(sprintf('Migration %s has no down-migration file', $this->version));
        }
        $sql = @file_get_contents($this->downPath);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Failed to read down-migration file: %s', $this->downPath));
        }

        return $sql;
    }

    public function checksum(): string
    {
        $payload = $this->readUpSql();
        if ($this->seedPath !== null) {
            $payload .= "\n-- seed --\n".$this->readSeedSql();
        }

        return hash('sha256', $payload);
    }
}
