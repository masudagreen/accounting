#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Entity 2 FS 補完スクリプト (repair-fs-tree.php)
 *
 * 目的:
 *   accountingLog で実際に使われている科目 ID が
 *   accountingFSJpn.jsonJgaapAccountTitlePL (+ BS/CR) の AccountTree に
 *   存在しない場合（G-9-1 参照）、その差分をレポートまたは補完する。
 *
 * 使い方:
 *   # dry-run (レポートのみ、DB 変更なし)
 *   DB_HOST=db DB_NAME=rucaro_golden DB_USER=rucaro DB_PASS=rucaro \
 *     php bin/repair-fs-tree.php
 *
 *   # 適用 (確認を求めた後に UPDATE を実行)
 *   DB_HOST=db DB_NAME=rucaro_golden DB_USER=rucaro DB_PASS=rucaro \
 *     php bin/repair-fs-tree.php --apply
 *
 * 警告:
 *   --apply 前に必ず DB バックアップを取得すること。
 *   dry-run 出力を確認してから --apply を実行すること。
 */

require_once __DIR__ . '/../vendor/autoload.php';

// -------------------------------------------------------------------------
// Option parsing
// -------------------------------------------------------------------------

$applyMode = in_array('--apply', $argv, true);

// -------------------------------------------------------------------------
// DB connection
// -------------------------------------------------------------------------

$host = (string) (getenv('DB_HOST') ?: 'db');
$port = (string) (getenv('DB_PORT') ?: '3306');
$name = (string) (getenv('DB_NAME') ?: '');
$user = (string) (getenv('DB_USER') ?: '');
$pass = (string) (getenv('DB_PASS') ?: '');

if ($name === '' || $user === '') {
    fwrite(STDERR, "ERROR: DB_NAME and DB_USER environment variables are required.\n");
    exit(1);
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    fwrite(STDERR, 'DB connection failed: ' . $e->getMessage() . "\n");
    exit(1);
}

// -------------------------------------------------------------------------
// Helpers
// -------------------------------------------------------------------------

/**
 * 元実装の `accountingFSJpn.jsonJgaapAccountTitle{PL,BS,CR}` の構造を再帰的に走査し、
 * `vars.idTarget` (真の科目ID) のみを収集する.
 *
 * JSON 構造例:
 *   [
 *     {
 *       "strTitle": "売上高",
 *       "vars": { "idTarget": "sales", "flagDebit": 0 },
 *       "child": [
 *         { "strTitle": "売上高", "vars": { "idTarget": "netSales", ... }, "child": [] },
 *         ...
 *       ]
 *     },
 *     ...
 *   ]
 *
 * @param mixed $node
 * @return list<string>
 */
function collectAccountTitleIds(mixed $node): array
{
    if (!is_array($node)) {
        return [];
    }

    $ids = [];

    // vars.idTarget があればそれが真の科目 ID
    if (isset($node['vars']) && is_array($node['vars'])) {
        $id = $node['vars']['idTarget'] ?? null;
        if (is_string($id) && $id !== '') {
            $ids[] = $id;
        }
    }

    // child 配列を再帰
    if (isset($node['child']) && is_array($node['child'])) {
        foreach ($node['child'] as $child) {
            array_push($ids, ...collectAccountTitleIds($child));
        }
    }

    // ルート配列 (数値キー) の場合は各要素を再帰
    foreach ($node as $key => $value) {
        if (is_int($key) && is_array($value)) {
            array_push($ids, ...collectAccountTitleIds($value));
        }
    }

    return array_values(array_unique($ids));
}

/**
 * Parse comma-delimited account title IDs from the accountingLog columns
 * (arrCommaIdAccountTitleDebit / arrCommaIdAccountTitleCredit).
 * Format: ",id1,id2,id3,"
 *
 * @return list<string>
 */
function parseCommaIds(string $raw): array
{
    $parts = array_filter(
        array_map('trim', explode(',', $raw)),
        static fn(string $s): bool => $s !== '',
    );

    return array_values($parts);
}

// -------------------------------------------------------------------------
// Step 1: fetch all entities + fiscal periods from accountingFSJpn
// -------------------------------------------------------------------------

$fsStmt = $pdo->query(
    'SELECT id, idEntity, numFiscalPeriod,
            jsonJgaapAccountTitlePL,
            jsonJgaapAccountTitleBS,
            jsonJgaapAccountTitleCR
       FROM accountingFSJpn
      ORDER BY idEntity, numFiscalPeriod'
);
assert($fsStmt !== false);
$fsRows = $fsStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($fsRows)) {
    echo "No rows found in accountingFSJpn. Nothing to do.\n";
    exit(0);
}

// -------------------------------------------------------------------------
// Step 2: for each entity+period, compare tree IDs vs log IDs
// -------------------------------------------------------------------------

/** @var array<string, array{fsRowId: int, idEntity: int, fiscalPeriod: int, column: string, missingIds: list<string>}> $allGaps */
$allGaps = [];

foreach ($fsRows as $fsRow) {
    $fsRowId       = (int) $fsRow['id'];
    $idEntity      = (int) $fsRow['idEntity'];
    $fiscalPeriod  = (int) $fsRow['numFiscalPeriod'];

    // Collect all known account title IDs from FS tree (PL + BS + CR)
    $knownIds = [];
    foreach (['jsonJgaapAccountTitlePL', 'jsonJgaapAccountTitleBS', 'jsonJgaapAccountTitleCR'] as $col) {
        $json = $fsRow[$col];
        if ($json === null || $json === '') {
            continue;
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            continue;
        }
        array_push($knownIds, ...collectAccountTitleIds($decoded));
    }
    $knownIds = array_flip(array_unique($knownIds));

    // Fetch account title IDs actually used in journal entries
    $stmt = $pdo->prepare(
        'SELECT arrCommaIdAccountTitleDebit, arrCommaIdAccountTitleCredit
           FROM accountingLog
          WHERE idEntity = ? AND numFiscalPeriod = ?'
    );
    $stmt->execute([$idEntity, $fiscalPeriod]);
    $logRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $usedIds = [];
    foreach ($logRows as $logRow) {
        foreach (['arrCommaIdAccountTitleDebit', 'arrCommaIdAccountTitleCredit'] as $col) {
            $raw = (string) ($logRow[$col] ?? '');
            if ($raw !== '') {
                array_push($usedIds, ...parseCommaIds($raw));
            }
        }
    }
    $usedIds = array_unique($usedIds);

    // Find IDs that are used but not in the tree
    $missing = [];
    foreach ($usedIds as $id) {
        if ($id === '' || isset($knownIds[$id])) {
            continue;
        }
        $missing[] = $id;
    }
    $missing = array_values(array_unique($missing));

    if (empty($missing)) {
        continue;
    }

    // Only report for PL tree (most common mismatch) — extend for BS/CR if needed
    $key = "entity{$idEntity}_period{$fiscalPeriod}";
    $allGaps[$key] = [
        'fsRowId'    => $fsRowId,
        'idEntity'   => $idEntity,
        'fiscalPeriod' => $fiscalPeriod,
        'column'     => 'jsonJgaapAccountTitlePL',
        'missingIds' => $missing,
    ];
}

// -------------------------------------------------------------------------
// Step 3: dry-run output
// -------------------------------------------------------------------------

if (empty($allGaps)) {
    echo "# FS Tree Repair Report\n\n";
    echo "No missing account titles found. All entities are consistent.\n";
    exit(0);
}

echo "# FS Tree Repair Report\n\n";
echo "The following account title IDs are used in `accountingLog` but are\n";
echo "**not defined** in the corresponding `accountingFSJpn` tree.\n\n";

foreach ($allGaps as $key => $gap) {
    echo "## Entity {$gap['idEntity']} / Fiscal Period {$gap['fiscalPeriod']}\n\n";
    echo "| # | Missing Account Title ID |\n";
    echo "|---|---------------------------|\n";
    foreach ($gap['missingIds'] as $i => $id) {
        echo '| ' . ($i + 1) . " | `$id` |\n";
    }
    echo "\n";
    echo "**Proposed action**: Add to `{$gap['column']}` under\n";
    echo "`sellingGeneralAndAdministrationExpenses` (default placement).\n\n";
}

// -------------------------------------------------------------------------
// Step 4: apply mode
// -------------------------------------------------------------------------

if (!$applyMode) {
    echo "---\n";
    echo "This is a **dry-run**. No changes were made.\n";
    echo "Review the report above, then re-run with `--apply` to patch the DB.\n";
    echo "\n";
    echo "IMPORTANT: Take a database backup before running with --apply.\n";
    exit(0);
}

// Safety confirmation
echo "---\n";
echo "WARNING: You are about to modify accountingFSJpn in the database.\n";
echo "Have you taken a backup? Type 'yes' to continue: ";

$answer = trim((string) fgets(STDIN));
if (strtolower($answer) !== 'yes') {
    echo "Aborted.\n";
    exit(0);
}

// Apply: patch each affected FS row
$defaultParent = 'sellingGeneralAndAdministrationExpenses';
$patchedCount  = 0;

foreach ($allGaps as $gap) {
    $stmt = $pdo->prepare(
        'SELECT jsonJgaapAccountTitlePL FROM accountingFSJpn WHERE id = ?'
    );
    $stmt->execute([$gap['fsRowId']]);
    $currentJson = (string) ($stmt->fetchColumn() ?: '');

    $tree = [];
    if ($currentJson !== '') {
        $decoded = json_decode($currentJson, true);
        if (is_array($decoded)) {
            $tree = $decoded;
        }
    }

    // Ensure the default parent node exists
    if (!isset($tree[$defaultParent]) || !is_array($tree[$defaultParent])) {
        $tree[$defaultParent] = [];
    }

    foreach ($gap['missingIds'] as $missingId) {
        if (!isset($tree[$defaultParent][$missingId])) {
            // Add a minimal node under the default parent
            $tree[$defaultParent][$missingId] = [];
            echo "  + Added `$missingId` under `$defaultParent`"
                . " (entity {$gap['idEntity']}, period {$gap['fiscalPeriod']})\n";
            ++$patchedCount;
        }
    }

    $newJson = (string) json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $update = $pdo->prepare(
        'UPDATE accountingFSJpn SET jsonJgaapAccountTitlePL = ?, stampUpdate = ? WHERE id = ?'
    );
    $update->execute([$newJson, time(), $gap['fsRowId']]);
}

echo "\nApplied $patchedCount account title additions.\n";
echo "Done. Re-run without --apply to verify the tree is now consistent.\n";
