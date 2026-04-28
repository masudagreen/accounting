#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Migration CLI
 *
 * Usage:
 *   php bin/migrate.php status
 *   php bin/migrate.php up
 *   php bin/migrate.php up --target=0002
 *   php bin/migrate.php down
 *   php bin/migrate.php down --target=0001
 *   php bin/migrate.php new <description>
 *
 * Environment variables:
 *   DB_HOST   (default: db)
 *   DB_PORT   (default: 3306)
 *   DB_NAME   (required)
 *   DB_USER   (required)
 *   DB_PASS   (required)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Migration\MigrationException;
use App\Infrastructure\Migration\MigrationRecord;
use App\Infrastructure\Migration\MigrationRunner;

// -------------------------------------------------------------------------
// Argument parsing
// -------------------------------------------------------------------------

$args = $argv;
array_shift($args); // remove script name

$command = array_shift($args) ?? 'help';
$options = [];

foreach ($args as $arg) {
    if (str_starts_with($arg, '--')) {
        $parts = explode('=', ltrim($arg, '-'), 2);
        $options[$parts[0]] = $parts[1] ?? true;
    } else {
        $options['_extra'][] = $arg;
    }
}

// -------------------------------------------------------------------------
// DB connection (skipped for 'new' command)
// -------------------------------------------------------------------------

$migrationsDir = __DIR__ . '/../migrations';

function buildDsn(): string
{
    $host = (string) ($_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db');
    $port = (string) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306');
    $name = (string) ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '');

    if ($name === '') {
        fwrite(STDERR, "ERROR: DB_NAME environment variable is required.\n");
        exit(1);
    }

    return "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
}

function buildPdo(): PDO
{
    $user = (string) ($_ENV['DB_USER'] ?? getenv('DB_USER') ?: '');
    $pass = (string) ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');

    if ($user === '') {
        fwrite(STDERR, "ERROR: DB_USER environment variable is required.\n");
        exit(1);
    }

    $pdo = new PDO(buildDsn(), $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

// -------------------------------------------------------------------------
// Commands
// -------------------------------------------------------------------------

try {
    match ($command) {
        'status' => cmdStatus($migrationsDir),
        'up'     => cmdUp($migrationsDir, $options['target'] ?? null),
        'down'   => cmdDown($migrationsDir, $options['target'] ?? null),
        'new'    => cmdNew($migrationsDir, $options['_extra'][0] ?? null),
        default  => cmdHelp(),
    };
} catch (MigrationException $e) {
    fwrite(STDERR, 'MIGRATION ERROR: ' . $e->getMessage() . "\n");
    exit(1);
} catch (PDOException $e) {
    fwrite(STDERR, 'DB ERROR: ' . $e->getMessage() . "\n");
    exit(1);
}

// -------------------------------------------------------------------------
// Command implementations
// -------------------------------------------------------------------------

function cmdStatus(string $migrationsDir): void
{
    $pdo    = buildPdo();
    $record = new MigrationRecord($pdo);
    $record->ensureTable();
    $runner = new MigrationRunner($record, $migrationsDir);

    $status = $runner->getStatus();

    if (empty($status)) {
        echo "No migration files found in $migrationsDir\n";
        return;
    }

    echo str_pad('Version', 10) . str_pad('Status', 10) . "\n";
    echo str_repeat('-', 20) . "\n";

    foreach ($status as $version => $state) {
        $symbol = $state === 'applied' ? '[ok]' : '[--]';
        echo str_pad($version, 10) . "$symbol $state\n";
    }
}

function cmdUp(string $migrationsDir, string|true|null $target): void
{
    $pdo    = buildPdo();
    $record = new MigrationRecord($pdo);
    $record->ensureTable();
    $runner = new MigrationRunner($record, $migrationsDir);

    if ($target !== null && $target !== true) {
        echo "Running UP migrations up to version $target ...\n";
        $runner->runUpTo($pdo, $target);
    } else {
        echo "Running all pending UP migrations ...\n";
        $runner->runUp($pdo);
    }

    echo "Done.\n";
}

function cmdDown(string $migrationsDir, string|true|null $target): void
{
    if ($target === null || $target === true) {
        fwrite(STDERR, "ERROR: down requires --target=NNNN\n");
        exit(1);
    }

    $pdo    = buildPdo();
    $record = new MigrationRecord($pdo);
    $record->ensureTable();
    $runner = new MigrationRunner($record, $migrationsDir);

    echo "Rolling back migrations down to version $target ...\n";
    $runner->runDownTo($pdo, $target);
    echo "Done.\n";
}

function cmdNew(string $migrationsDir, ?string $description): void
{
    if ($description === null || $description === '') {
        fwrite(STDERR, "ERROR: new requires a description, e.g.: php bin/migrate.php new add_invoice_columns\n");
        exit(1);
    }

    // Use a dummy record that reports no applied versions (no DB needed)
    $record = new class implements \App\Infrastructure\Migration\MigrationRecordInterface {
        public function getAppliedVersions(): array { return []; }
        public function isApplied(string $version): bool { return false; }
        public function markApplied(string $version): void {}
        public function markRolledBack(string $version): void {}
    };

    $runner   = new MigrationRunner($record, $migrationsDir);
    $basename = $runner->buildNewFilename($description);

    $upPath   = $migrationsDir . '/' . $basename . '.up.sql';
    $downPath = $migrationsDir . '/' . $basename . '.down.sql';

    file_put_contents($upPath,   "-- Migration {$basename}: TODO\n");
    file_put_contents($downPath, "-- Migration {$basename} rollback: TODO\n");

    echo "Created:\n  $upPath\n  $downPath\n";
}

function cmdHelp(): void
{
    echo <<<HELP
    Usage: php bin/migrate.php <command> [options]

    Commands:
      status                     Show applied and pending migrations
      up [--target=NNNN]         Apply pending migrations (optionally up to NNNN)
      down --target=NNNN         Rollback migrations down to NNNN (exclusive)
      new <description>          Create new empty migration files

    Environment variables (required for status/up/down):
      DB_HOST   Database host       (default: db)
      DB_PORT   Database port       (default: 3306)
      DB_NAME   Database name       (required)
      DB_USER   Database user       (required)
      DB_PASS   Database password   (required)

    Examples:
      DB_HOST=db DB_NAME=rucaro_test DB_USER=rucaro DB_PASS=rucaro php bin/migrate.php status
      DB_HOST=db DB_NAME=rucaro_test DB_USER=rucaro DB_PASS=rucaro php bin/migrate.php up
      DB_HOST=db DB_NAME=rucaro_test DB_USER=rucaro DB_PASS=rucaro php bin/migrate.php down --target=0001
      php bin/migrate.php new add_invoice_columns

    HELP;
}
