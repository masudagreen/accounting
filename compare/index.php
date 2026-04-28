<?php

declare(strict_types=1);

/**
 * Shadow Web UI - Compare Front Controller.
 *
 * /compare/ ディレクトリの入口. GET リクエストのみ処理する.
 * 認証は legacy の baseSession テーブルを参照する.
 *
 * セキュリティ:
 *  - 出力はすべて htmlspecialchars エスケープ済み
 *  - SQL はプリペアドステートメントのみ
 *  - POST は .htaccess でブロック
 *  - 本番データをログに出力しない
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Application\Service\FinancialStatementService;
use App\Application\Service\TrialBalanceService;
use App\Compare\Auth\SessionAuthenticator;
use App\Compare\Page\BalanceSheetPage;
use App\Compare\Page\HomePage;
use App\Compare\Page\JournalListPage;
use App\Compare\Page\ProfitLossPage;
use App\Compare\Page\TrialBalancePage;
use App\Compare\Routing\Router;
use App\Infrastructure\Persistence\Mariadb\MariadbAccountTreeRepository;
use App\Infrastructure\Persistence\Mariadb\MariadbJournalRepository;

// ─── DB 接続 ────────────────────────────────────────────────────────────────
$host = 'db';
$port = '3306';
$db   = 'rucaro';
$user = 'rucaro';
$pass = 'rucaro';

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
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"><title>DB エラー</title></head>';
    echo '<body><h1>データベース接続エラー</h1><p>システム管理者に連絡してください。</p></body></html>';
    exit;
}

// ─── 認証 ────────────────────────────────────────────────────────────────────
$authenticator = new SessionAuthenticator($pdo);
$cookies       = $_COOKIE;

// array<string, string> に変換
$cookiesStr = [];
foreach ($cookies as $k => $v) {
    $cookiesStr[(string) $k] = (string) $v;
}

if (! $authenticator->authenticate($cookiesStr)) {
    // 未認証は legacy ログインページへリダイレクト
    header('Location: /');
    exit;
}

// ─── ルーティング ────────────────────────────────────────────────────────────
$router = new Router();
/** @var array<string, mixed> $queryParams */
$queryParams = $_GET;
$page        = $router->resolve($queryParams);

$idEntity        = max(1, (int) ($queryParams['entity'] ?? 1));
$numFiscalPeriod = max(1, (int) ($queryParams['period'] ?? 1));

// ─── ページレンダリング ──────────────────────────────────────────────────────
try {
    $html = match ($page) {
        'home'          => (new HomePage($pdo))->render(),
        'trial-balance' => (function () use ($pdo, $idEntity, $numFiscalPeriod): string {
            $journalRepo = new MariadbJournalRepository($pdo);
            $treeRepo    = new MariadbAccountTreeRepository($pdo);
            $service     = new TrialBalanceService($journalRepo, $treeRepo);
            return (new TrialBalancePage($pdo, $service))->render($idEntity, $numFiscalPeriod);
        })(),
        'profit-loss'   => (function () use ($pdo, $idEntity, $numFiscalPeriod): string {
            $journalRepo = new MariadbJournalRepository($pdo);
            $treeRepo    = new MariadbAccountTreeRepository($pdo);
            $service     = new FinancialStatementService($journalRepo, $treeRepo);
            return (new ProfitLossPage($pdo, $service))->render($idEntity, $numFiscalPeriod);
        })(),
        'balance-sheet' => (function () use ($pdo, $idEntity, $numFiscalPeriod): string {
            $journalRepo = new MariadbJournalRepository($pdo);
            $treeRepo    = new MariadbAccountTreeRepository($pdo);
            $service     = new FinancialStatementService($journalRepo, $treeRepo);
            return (new BalanceSheetPage($pdo, $service))->render($idEntity, $numFiscalPeriod);
        })(),
        'journal-list'  => (new JournalListPage($pdo))->render($idEntity, $numFiscalPeriod),
        default         => (new HomePage($pdo))->render(),
    };
} catch (Throwable $e) {
    // エラー詳細はサーバーサイドにのみ記録し、ユーザーには見せない
    error_log('[compare] Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    $html = '<!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"><title>エラー</title></head>'
          . '<body><h1>エラーが発生しました</h1><p>再度お試しください。</p></body></html>';
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
echo $html;
