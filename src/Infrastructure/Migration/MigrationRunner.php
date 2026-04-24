<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Migration;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Scans scripts/migrate/ for numbered .sql files and applies them in order.
 *
 * Conventions:
 *   - Up migration file:   NNNN_<slug>.sql
 *   - Down migration file: NNNN_<slug>.down.sql
 *   - Version 0000 is a bootstrap file. It is NOT recorded in
 *     schema_migrations and is NOT applied by up()/down(). It must be
 *     executed manually before the runner is first used (because
 *     schema_migrations itself is created by 0000).
 *
 * Each applied migration is recorded in schema_migrations with its
 * SHA-256 checksum, so silent file rewrites can be detected.
 */
final class MigrationRunner
{
    private const BOOTSTRAP_VERSION = '0000';

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $migrationsDir,
    ) {
    }

    /**
     * Apply every pending migration in ascending version order.
     *
     * @return int Number of migrations applied.
     */
    public function up(): int
    {
        $this->ensureHistoryTable();

        $applied = $this->loadAppliedVersions();
        $pending = array_filter(
            $this->discover(),
            static fn (Migration $m): bool =>
                $m->version !== self::BOOTSTRAP_VERSION
                && !isset($applied[$m->version]),
        );

        $count = 0;
        foreach ($pending as $migration) {
            $this->executeSql($migration->readUpSql());
            $this->recordApplied($migration);
            $count++;
        }
        return $count;
    }

    /**
     * Roll back the most recently applied migrations.
     *
     * @param int $step Number of migrations to roll back (default 1).
     * @return int Number of migrations actually rolled back.
     */
    public function down(int $step = 1): int
    {
        if ($step < 1) {
            throw new \InvalidArgumentException('step must be >= 1');
        }
        $this->ensureHistoryTable();

        $all = $this->discover();
        $byVersion = [];
        foreach ($all as $m) {
            $byVersion[$m->version] = $m;
        }

        $applied = array_keys($this->loadAppliedVersions());
        rsort($applied, SORT_STRING);
        $targets = array_slice($applied, 0, $step);

        $count = 0;
        foreach ($targets as $version) {
            if (!isset($byVersion[$version])) {
                throw new RuntimeException(sprintf(
                    'Applied migration %s has no matching file on disk; cannot roll back.',
                    $version,
                ));
            }
            $migration = $byVersion[$version];
            if (!$migration->hasDown()) {
                throw new RuntimeException(sprintf(
                    'Migration %s has no .down.sql file; cannot roll back.',
                    $version,
                ));
            }
            $this->executeSql($migration->readDownSql());
            $this->forgetApplied($version);
            $count++;
        }
        return $count;
    }

    /**
     * Return status of every discovered migration (applied or pending).
     *
     * @return list<array{version: string, name: string, applied: bool, applied_at: ?string, bootstrap: bool}>
     */
    public function status(): array
    {
        $this->ensureHistoryTable();
        $applied = $this->loadAppliedVersions();

        $rows = [];
        foreach ($this->discover() as $migration) {
            $isBootstrap = $migration->version === self::BOOTSTRAP_VERSION;
            $rows[] = [
                'version'    => $migration->version,
                'name'       => $migration->name,
                'applied'    => $isBootstrap ? true : isset($applied[$migration->version]),
                'applied_at' => $applied[$migration->version] ?? null,
                'bootstrap'  => $isBootstrap,
            ];
        }
        return $rows;
    }

    /**
     * Discover migrations on disk and return them in ascending version order.
     *
     * @return list<Migration>
     */
    public function discover(): array
    {
        if (!is_dir($this->migrationsDir)) {
            throw new RuntimeException(sprintf(
                'Migrations directory does not exist: %s',
                $this->migrationsDir,
            ));
        }

        $upFiles = glob(rtrim($this->migrationsDir, '/\\') . DIRECTORY_SEPARATOR . '*.sql') ?: [];
        $byVersion = [];

        foreach ($upFiles as $path) {
            $basename = basename($path);
            if (str_ends_with($basename, '.down.sql')) {
                continue;
            }
            if (!preg_match('/^(\d{4})_([A-Za-z0-9_\-]+)\.sql$/', $basename, $m)) {
                continue;
            }
            [$version, $name] = [$m[1], $m[2]];
            $downPath = substr($path, 0, -4) . '.down.sql';
            $byVersion[$version] = new Migration(
                version: $version,
                name: $name,
                upPath: $path,
                downPath: is_file($downPath) ? $downPath : null,
            );
        }

        ksort($byVersion, SORT_STRING);
        return array_values($byVersion);
    }

    private function ensureHistoryTable(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations ('
            . ' version VARCHAR(32) NOT NULL PRIMARY KEY,'
            . ' applied_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),'
            . ' checksum CHAR(64) NULL DEFAULT NULL'
            . ') ENGINE=InnoDB'
            . ' DEFAULT CHARACTER SET utf8mb4'
            . ' COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @return array<string, string> version => applied_at
     */
    private function loadAppliedVersions(): array
    {
        $stmt = $this->pdo->query(
            'SELECT version, applied_at FROM schema_migrations ORDER BY version ASC'
        );
        if ($stmt === false) {
            return [];
        }
        $out = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $out[(string) $row['version']] = (string) $row['applied_at'];
        }
        return $out;
    }

    private function recordApplied(Migration $migration): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO schema_migrations (version, checksum) VALUES (:v, :c)'
        );
        $stmt->execute([
            ':v' => $migration->version,
            ':c' => $migration->checksum(),
        ]);
    }

    private function forgetApplied(string $version): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM schema_migrations WHERE version = :v'
        );
        $stmt->execute([':v' => $version]);
    }

    /**
     * Execute a SQL blob that may contain multiple statements.
     *
     * MariaDB DDL performs implicit commits, so we do not wrap in a single
     * transaction; we simply run statements sequentially and let the first
     * failure propagate.
     */
    private function executeSql(string $sql): void
    {
        $statements = $this->splitStatements($sql);
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed === '') {
                continue;
            }
            try {
                $this->pdo->exec($trimmed);
            } catch (PDOException $e) {
                throw new RuntimeException(
                    sprintf('Migration statement failed: %s', $e->getMessage()),
                    0,
                    $e,
                );
            }
        }
    }

    /**
     * Naive SQL splitter: splits on ';' that appear outside of single/double
     * quoted strings and line comments. Good enough for our DDL files.
     *
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inSingle = false;
        $inDouble = false;
        $inLineComment = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            $next = $i + 1 < $len ? $sql[$i + 1] : '';

            if ($inLineComment) {
                $buffer .= $ch;
                if ($ch === "\n") {
                    $inLineComment = false;
                }
                continue;
            }

            if (!$inSingle && !$inDouble && $ch === '-' && $next === '-') {
                $inLineComment = true;
                $buffer .= $ch;
                continue;
            }

            if (!$inDouble && $ch === "'" && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inSingle = !$inSingle;
            } elseif (!$inSingle && $ch === '"' && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inDouble = !$inDouble;
            }

            if ($ch === ';' && !$inSingle && !$inDouble) {
                $statements[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $ch;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }
        return $statements;
    }
}
