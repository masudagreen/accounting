<?php
declare(strict_types=1);

/**
 * Ad-hoc migration runner for Phase A reality check.
 *
 * Note: MigrationRunner uses glob '*.sql' which captures `_seed.sql` files
 * with the same 4-digit version, causing the seed file to overwrite the
 * primary file in its byVersion map (alphabetical order: `0008_x.sql` <
 * `0008_x_seed.sql`). Seeds end up applied without their schema. We work
 * around it here by:
 *   1. Applying primary migrations in numeric order (skip *_seed.sql).
 *   2. Applying *_seed.sql files in numeric order afterwards.
 * The schema_migrations history records each version once.
 */

require __DIR__ . '/../../vendor/autoload.php';

$host = getenv('DB_HOST') ?: 'db';
$port = (int)(getenv('DB_PORT_INTERNAL') ?: 3306);
$db   = getenv('DB_NAME') ?: 'rucaro';
$user = getenv('DB_USER') ?: 'rucaro';
$pass = getenv('DB_PASSWORD') ?: 'rucaro';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $db);
echo "DSN: {$dsn}\n";

$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$dir = __DIR__;

// Ensure schema_migrations exists.
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations ('
    . ' version VARCHAR(32) NOT NULL PRIMARY KEY,'
    . ' applied_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),'
    . ' checksum CHAR(64) NULL DEFAULT NULL'
    . ') ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
);

$applied = [];
foreach ($pdo->query('SELECT version FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN) as $v) {
    $applied[(string)$v] = true;
}

// Collect files
$primary = []; // version => path
$seeds   = []; // numeric order list
foreach (glob($dir . '/*.sql') as $path) {
    $b = basename($path);
    if (str_ends_with($b, '.down.sql')) continue;
    if (preg_match('/^(\d{4})_(.+)_seed\.sql$/', $b, $m)) {
        $seeds[] = [$m[1], $b, $path];
        continue;
    }
    if (preg_match('/^(\d{4})_(.+)\.sql$/', $b, $m)) {
        if ($m[1] === '0000') continue; // bootstrap, applied separately
        $primary[$m[1]] = $path;
    }
}
ksort($primary, SORT_STRING);
usort($seeds, fn($a, $b) => strcmp($a[0], $b[0]));

function splitStatements(string $sql): array {
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
            if ($ch === "\n") $inLineComment = false;
            continue;
        }
        if (!$inSingle && !$inDouble && $ch === '-' && $next === '-') {
            $inLineComment = true;
            $buffer .= $ch;
            continue;
        }
        if (!$inDouble && $ch === "'" && ($i === 0 || $sql[$i - 1] !== '\\')) $inSingle = !$inSingle;
        elseif (!$inSingle && $ch === '"' && ($i === 0 || $sql[$i - 1] !== '\\')) $inDouble = !$inDouble;
        if ($ch === ';' && !$inSingle && !$inDouble) {
            $statements[] = $buffer;
            $buffer = '';
            continue;
        }
        $buffer .= $ch;
    }
    if (trim($buffer) !== '') $statements[] = $buffer;
    return $statements;
}

function execSqlFile(PDO $pdo, string $path, string $label): void {
    $sql = file_get_contents($path);
    foreach (splitStatements($sql) as $stmt) {
        $t = trim($stmt);
        if ($t === '') continue;
        try {
            $pdo->exec($t);
        } catch (PDOException $e) {
            fwrite(STDERR, "[FAIL] {$label}: {$e->getMessage()}\nSQL: " . substr($t, 0, 200) . "...\n");
            throw $e;
        }
    }
}

$insert = $pdo->prepare('INSERT INTO schema_migrations (version, checksum) VALUES (:v, :c)');

$appliedCount = 0;
foreach ($primary as $version => $path) {
    if (isset($applied[$version])) {
        echo "  [SKIP] {$version} already applied\n";
        continue;
    }
    echo "  [APPLY] {$version} " . basename($path) . "\n";
    execSqlFile($pdo, $path, $version);
    $insert->execute([':v' => $version, ':c' => hash_file('sha256', $path)]);
    $appliedCount++;
}

$seedCount = 0;
foreach ($seeds as [$version, $name, $path]) {
    echo "  [SEED ] {$version} {$name}\n";
    execSqlFile($pdo, $path, $name);
    $seedCount++;
}

echo "\nApplied {$appliedCount} primary migrations + {$seedCount} seeds.\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Total tables: " . count($tables) . "\n";
foreach ($tables as $t) echo "  - {$t}\n";
