<?php

declare(strict_types=1);

/**
 * 現新比較レポート CLI.
 *
 * 本番DBスナップショット (rucaro_golden) を読み、新ドメインで再計算した値を
 * 並べた Markdown レポートを出力する.
 *
 * 使い方 (Docker コンテナ内):
 *   docker compose exec -T app php bin/compare-report.php > report.md
 *
 * 環境変数:
 *   GOLDEN_DB_HOST  (default: db)
 *   GOLDEN_DB_PORT  (default: 3306)
 *   GOLDEN_DB_NAME  (default: rucaro_golden)
 *   GOLDEN_DB_USER  (default: rucaro)
 *   GOLDEN_DB_PASS  (default: rucaro)
 *
 * 出力:
 *   - 事業体・期ごとの仕訳件数
 *   - 借方=貸方 不変条件チェック
 *   - 当期純利益: 本番DB値 ⇄ 新ドメイン計算値 ⇄ 差額
 *   - 本ファイルは個人情報や金額自体は載せず、件数・不変条件結果・差額のみ
 *
 * セキュリティ: 出力に金額は含まれるが社名・個人名は含まない (idEntity と numFiscalPeriod のみ).
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\StandardChartLoader;
use App\Domain\FinancialStatement\ProfitAndLossBuilder;
use App\Domain\Ledger\Ledger;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use App\Infrastructure\Legacy\LegacyAccountTreeReader;
use App\Infrastructure\Legacy\LegacyJournalReader;

$host = getenv('GOLDEN_DB_HOST') ?: 'db';
$port = getenv('GOLDEN_DB_PORT') ?: '3306';
$db   = getenv('GOLDEN_DB_NAME') ?: 'rucaro_golden';
$user = getenv('GOLDEN_DB_USER') ?: 'rucaro';
$pass = getenv('GOLDEN_DB_PASS') ?: 'rucaro';

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db),
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    );
} catch (PDOException $e) {
    fwrite(STDERR, "DB connection failed: {$e->getMessage()}\n");
    fwrite(STDERR, "Hints:\n");
    fwrite(STDERR, "  1. db20260207.sql.zip を unzip して tests/Golden/data/ に置く\n");
    fwrite(STDERR, "  2. docker compose exec db sh -c 'mysql -urucaro -prucaro -e \"CREATE DATABASE rucaro_golden\"'\n");
    fwrite(STDERR, "  3. docker compose exec -T db mysql -urucaro -prucaro rucaro_golden < tests/Golden/data/db20260207.sql\n");
    exit(1);
}

echo "# 現新比較レポート\n\n";
echo sprintf("生成日時: %s\n\n", date('Y-m-d H:i:s'));
echo sprintf("DB: `%s` (%s:%s)\n\n", $db, $host, $port);

// ------------------------------------------------------------------
// 1. 概要: テーブルと事業体
// ------------------------------------------------------------------
echo "## 1. データ概要\n\n";

$entitiesStmt = $pdo->query('SELECT id, strTitle FROM accountingEntity ORDER BY id');
assert($entitiesStmt !== false);
$entities = $entitiesStmt->fetchAll();
echo "| 事業体ID | 名称 |\n|---:|---|\n";
foreach ($entities as $e) {
    // 名称は先頭数文字のみマスク
    $masked = mb_substr((string) $e['strTitle'], 0, 1) . str_repeat('*', max(0, mb_strlen((string) $e['strTitle']) - 1));
    echo sprintf("| %d | %s |\n", $e['id'], $masked);
}
echo "\n";

$summaryStmt = $pdo->query(
    "SELECT idEntity, numFiscalPeriod,
            SUM(CASE WHEN flagRemove = 0 THEN 1 ELSE 0 END) AS active_logs,
            SUM(CASE WHEN flagRemove = 1 THEN 1 ELSE 0 END) AS removed_logs
     FROM accountingLog
     GROUP BY idEntity, numFiscalPeriod
     ORDER BY idEntity, numFiscalPeriod"
);
assert($summaryStmt !== false);
$summary = $summaryStmt->fetchAll();

echo "## 2. 仕訳件数\n\n";
echo "| 事業体 | 期 | 有効仕訳 | 論理削除 |\n|---:|---:|---:|---:|\n";
foreach ($summary as $row) {
    echo sprintf(
        "| %d | %d | %d | %d |\n",
        $row['idEntity'],
        $row['numFiscalPeriod'],
        $row['active_logs'],
        $row['removed_logs'],
    );
}
echo "\n";

// ------------------------------------------------------------------
// 3. 各事業体・期での比較
// ------------------------------------------------------------------
echo "## 3. 事業体・期別の比較\n\n";
echo "凡例:\n";
echo "- ✓ = 借方=貸方 / 当期純利益が完全一致\n";
echo "- ✗ = 不一致 (差額または借方≠貸方)\n";
echo "- — = データ不足で計算不能\n\n";

echo "| 事業体 | 期 | 仕訳件数 | TB借方=貸方 | 本番DB 純利益 | 新ドメイン 純利益 | 差額 |\n";
echo "|---:|---:|---:|:---:|---:|---:|---:|\n";

$journalReader = new LegacyJournalReader();
$accountTreeReader = new LegacyAccountTreeReader();

$totalRows = 0;
$matchCount = 0;
$balanceOkCount = 0;

foreach ($summary as $row) {
    $idEntity = (int) $row['idEntity'];
    $period = (int) $row['numFiscalPeriod'];
    $logs = (int) $row['active_logs'];
    $totalRows++;

    if ($logs === 0) {
        echo sprintf("| %d | %d | 0 | — | — | — | — |\n", $idEntity, $period);
        continue;
    }

    // 仕訳ロード
    $logRows = $pdo->prepare(
        'SELECT idLog, idEntity, numFiscalPeriod, stampBook, flagRemove, jsonVersion
         FROM accountingLog
         WHERE idEntity = ? AND numFiscalPeriod = ? AND flagRemove = 0'
    );
    $logRows->execute([$idEntity, $period]);
    /** @var list<array<string, mixed>> $rows */
    $rows = array_values($logRows->fetchAll());

    $reconstruction = $journalReader->read($rows);
    $entries = $reconstruction['entries'];

    if (count($entries) === 0) {
        echo sprintf("| %d | %d | %d | — | — | — | — |\n", $idEntity, $period, $logs);
        continue;
    }

    // AccountTree (本番のFS設定から). BS + PL を統合したツリーを使用.
    try {
        $fsRow = $pdo->prepare(
            'SELECT jsonJgaapAccountTitleBS, jsonJgaapAccountTitlePL
             FROM accountingFSJpn
             WHERE idEntity = ? AND numFiscalPeriod = ? LIMIT 1'
        );
        $fsRow->execute([$idEntity, $period]);
        $fs = $fsRow->fetch();
        if (! $fs || ! is_string($fs['jsonJgaapAccountTitleBS']) || ! is_string($fs['jsonJgaapAccountTitlePL'])) {
            echo sprintf("| %d | %d | %d | — | — | — | error: FS data missing |\n", $idEntity, $period, $logs);
            continue;
        }
        $tree = $accountTreeReader->buildCombinedTreeFromJson(
            $fs['jsonJgaapAccountTitleBS'],
            $fs['jsonJgaapAccountTitlePL'],
        );
    } catch (Throwable $e) {
        echo sprintf("| %d | %d | %d | — | — | — | error: %s |\n", $idEntity, $period, $logs, $e->getMessage());
        continue;
    }

    // 試算表
    $ledger = Ledger::fromJournalEntries($entries);
    $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
    $balanced = $tb->totalDebits()->equals($tb->totalCredits());
    if ($balanced) {
        $balanceOkCount++;
    }

    // 新ドメインの当期純利益
    $pl = ProfitAndLossBuilder::build($tree, $tb);
    $newNetIncome = $pl->netIncome();

    // 本番DBの当期純利益: accountingFSValueJpn から currentTermProfitOrLossNet を引く
    $oldNetIncome = fetchOldNetIncome($pdo, $idEntity, $period);

    $diff = $oldNetIncome === null
        ? null
        : $newNetIncome->minus($oldNetIncome);

    $matched = $oldNetIncome !== null && $diff !== null && $diff->isZero();
    if ($matched) {
        $matchCount++;
    }

    echo sprintf(
        "| %d | %d | %d | %s | %s | %s | %s |\n",
        $idEntity,
        $period,
        $logs,
        $balanced ? '✓' : '✗',
        $oldNetIncome === null ? '—' : formatYen($oldNetIncome),
        formatYen($newNetIncome),
        $diff === null ? '—' : formatYen($diff),
    );
}

echo "\n";

// ------------------------------------------------------------------
// 4. サマリ
// ------------------------------------------------------------------
echo "## 4. サマリ\n\n";
echo sprintf("- 検証期数: **%d**\n", $totalRows);
echo sprintf("- 借方=貸方 一致: **%d** / %d\n", $balanceOkCount, $totalRows);
echo sprintf("- 当期純利益 完全一致: **%d** / %d\n", $matchCount, $totalRows);
echo "\n";

if ($matchCount === $totalRows) {
    echo "**結論**: 全期で新ドメインと本番DBの当期純利益が完全一致 ✓\n";
} elseif ($balanceOkCount === $totalRows) {
    echo "**結論**: 借方=貸方は全期成立。純利益差は集計ロジックの違いか丸めの違いを精査せよ。\n";
} else {
    echo "**結論**: 借方≠貸方の期があり。`docs/ai/06_known_issues.md` G-9-1 を参照 (本番DB側の FS 設定不整合の可能性).\n";
}

echo "\n";
echo "---\n";
echo "詳細ログは `docker compose exec -T app vendor/bin/phpunit --testsuite=golden` で確認可。\n";

// ------------------------------------------------------------------
// helpers
// ------------------------------------------------------------------

/**
 * 本番DBの保存値から「当期純利益」相当を取得する.
 * accountingFSValueJpn.jsonJgaapFSPL の f1.currentTermProfitOrLossNet.sumNext.
 */
function fetchOldNetIncome(PDO $pdo, int $idEntity, int $period): ?Money
{
    $stmt = $pdo->prepare(
        'SELECT jsonJgaapFSPL FROM accountingFSValueJpn
         WHERE idEntity = ? AND numFiscalPeriod = ? LIMIT 1'
    );
    $stmt->execute([$idEntity, $period]);
    $row = $stmt->fetch();
    if (! $row || ! is_string($row['jsonJgaapFSPL'])) {
        return null;
    }
    $json = json_decode($row['jsonJgaapFSPL'], true);
    if (! is_array($json)) {
        return null;
    }

    foreach (['f1', 'f21', 'f22'] as $key) {
        $section = $json[$key] ?? null;
        if (! is_array($section)) {
            continue;
        }
        $netItem = $section['currentTermProfitOrLossNet'] ?? null;
        if (! is_array($netItem)) {
            continue;
        }
        $sumNext = $netItem['sumNext'] ?? null;
        if ($sumNext !== null) {
            return Money::ofYen((string) $sumNext);
        }
    }
    return null;
}

/**
 * (未使用 - 後方互換のため残す) accountingFSValueJpn ノードから値を抽出.
 */
function extractValueFromAccountNode(mixed $node): ?string
{
    if (! is_array($node)) {
        return null;
    }
    $keys = ['f1', 'sumNext'];
    foreach ($keys as $k) {
        if (isset($node[$k]) && is_numeric($node[$k])) {
            return (string) $node[$k];
        }
    }
    return null;
}

function formatYen(Money $m): string
{
    return number_format((int) (float) $m->toString()) . ' 円';
}
