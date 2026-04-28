<?php

declare(strict_types=1);

namespace App\Tests\Integration\Compare;

use App\Compare\Auth\SessionAuthenticator;
use App\Compare\Page\BalanceSheetPage;
use App\Compare\Page\HomePage;
use App\Compare\Page\JournalListPage;
use App\Compare\Page\ProfitLossPage;
use App\Compare\Page\TrialBalancePage;
use App\Infrastructure\Persistence\Mariadb\MariadbAccountTreeRepository;
use App\Infrastructure\Persistence\Mariadb\MariadbJournalRepository;
use App\Application\Service\FinancialStatementService;
use App\Application\Service\TrialBalanceService;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * 各比較ページのレンダリング統合テスト.
 *
 * rucaro DB が利用可能な環境で実行する.
 * DB が無い場合 (TEST_DB_DSN 未設定) はスキップする.
 */
final class PageRenderingIntegrationTest extends TestCase
{
    private PDO $pdo;
    private int $idEntity    = 1;
    private int $numFiscalPeriod = 14;

    protected function setUp(): void
    {
        $dsn = getenv('TEST_DB_DSN');
        if (! $dsn) {
            $host = 'db';
            $port = '3306';
            $db   = 'rucaro';
            $user = 'rucaro';
            $pass = 'rucaro';
            try {
                $pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
                );
                $this->pdo = $pdo;
            } catch (\PDOException) {
                $this->markTestSkipped('TEST_DB_DSN not configured — skipping integration tests');
            }
        } else {
            $this->pdo = new PDO(
                $dsn,
                getenv('TEST_DB_USER') ?: 'rucaro',
                getenv('TEST_DB_PASS') ?: 'rucaro',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );
        }
    }

    public function testHomePageRendersWithoutException(): void
    {
        $page = new HomePage($this->pdo);
        $html = $page->render();

        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('Shadow Mode', $html);
    }

    public function testTrialBalancePageRendersWithoutException(): void
    {
        $journalRepo = new MariadbJournalRepository($this->pdo);
        $treeRepo    = new MariadbAccountTreeRepository($this->pdo);
        $service     = new TrialBalanceService($journalRepo, $treeRepo);

        $page = new TrialBalancePage($this->pdo, $service);
        $html = $page->render($this->idEntity, $this->numFiscalPeriod);

        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('試算表', $html);
    }

    public function testProfitLossPageRendersWithoutException(): void
    {
        $journalRepo = new MariadbJournalRepository($this->pdo);
        $treeRepo    = new MariadbAccountTreeRepository($this->pdo);
        $service     = new FinancialStatementService($journalRepo, $treeRepo);

        $page = new ProfitLossPage($this->pdo, $service);
        $html = $page->render($this->idEntity, $this->numFiscalPeriod);

        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('損益', $html);
    }

    public function testBalanceSheetPageRendersWithoutException(): void
    {
        $journalRepo = new MariadbJournalRepository($this->pdo);
        $treeRepo    = new MariadbAccountTreeRepository($this->pdo);
        $service     = new FinancialStatementService($journalRepo, $treeRepo);

        $page = new BalanceSheetPage($this->pdo, $service);
        $html = $page->render($this->idEntity, $this->numFiscalPeriod);

        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('貸借', $html);
    }

    public function testJournalListPageRendersWithoutException(): void
    {
        $page = new JournalListPage($this->pdo);
        $html = $page->render($this->idEntity, $this->numFiscalPeriod);

        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('仕訳', $html);
    }
}
