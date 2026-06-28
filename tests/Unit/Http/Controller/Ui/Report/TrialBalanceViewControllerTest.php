<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;
use Rucaro\Http\Controller\Ui\Report\TrialBalanceViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(TrialBalanceViewController::class)]
final class TrialBalanceViewControllerTest extends TestCase
{
    /**
     * Valid Crockford-base32 ULIDs for tests. Avoid letters U/L/I/O.
     */
    private const ENTITY_ID = '01HW7K9B2QV7C8Y4ZENTITY0001';
    private const FISCAL_TERM_ID = '01HW7K9B2QV7C8Y4ZFISCAL0001';

    #[\Override]
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    #[\Override]
    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRedirectsToDashboardWhenNoEntitySelected(): void
    {
        $controller = $this->buildController(new SessionStore());
        $request = new ServerRequest('GET', '/ui/trial-balance', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testRedirectsToDashboardWhenNoFiscalTermAvailable(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::ENTITY_ID);
        // No fiscal term in session and the in-memory PDO has none for the entity.

        $controller = $this->buildController($session);
        $request = new ServerRequest('GET', '/ui/trial-balance', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testRendersHtmlWithBalancedTotals(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::ENTITY_ID);
        $session->setSelectedFiscalTerm(self::FISCAL_TERM_ID);

        $rows = [
            // Cash: opening 3,897,237 + dr 9,000,000 - cr 7,406,800 = 5,490,437
            TrialBalanceRow::compute(
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCOUNT001',
                accountTitleCode: 'L0004',
                accountTitleName: '現金',
                accountCategory: 'asset',
                normalSide: TrialBalanceRow::NORMAL_DEBIT,
                debitTotal: '9000000.0000',
                creditTotal: '7406800.0000',
                lineCount: 12,
                openingBalance: '3897237.0000',
            ),
            TrialBalanceRow::compute(
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCOUNT002',
                accountTitleCode: 'L0010',
                accountTitleName: '通信費',
                accountCategory: 'expense',
                normalSide: TrialBalanceRow::NORMAL_DEBIT,
                debitTotal: '100000.0000',
                creditTotal: '0.0000',
                lineCount: 1,
            ),
            TrialBalanceRow::compute(
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCOUNT003',
                accountTitleCode: 'L0050',
                accountTitleName: '売上高',
                accountCategory: 'revenue',
                normalSide: TrialBalanceRow::NORMAL_CREDIT,
                debitTotal: '0.0000',
                creditTotal: '1693200.0000',
                lineCount: 4,
            ),
        ];

        $controller = $this->buildController($session, rows: $rows);
        $request = new ServerRequest('GET', '/ui/trial-balance', [], [], null, '');

        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertSame('text/html; charset=utf-8', $response->headers['Content-Type'] ?? '');
        self::assertStringContainsString('合計残高試算表', $response->body);
        self::assertStringContainsString('L0004', $response->body);
        self::assertStringContainsString('現金', $response->body);
        self::assertStringContainsString('通信費', $response->body);
        self::assertStringContainsString('売上高', $response->body);
        // Totals: debit = 9_000_000 + 100_000 + 0 = 9_100_000
        // Credit = 7_406_800 + 0 + 1_693_200 = 9_100_000
        // Both formatted with thousand separators.
        self::assertStringContainsString('9,100,000', $response->body);
        // Balance check OK marker.
        self::assertStringContainsString('一致', $response->body);
        // Opening balance for cash row should appear.
        self::assertStringContainsString('3,897,237', $response->body);
    }

    public function testRespectsYearAndMonthQueryParams(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::ENTITY_ID);
        $session->setSelectedFiscalTerm(self::FISCAL_TERM_ID);

        $query = new SpyTbQuery();
        $controller = $this->buildController($session, query: $query);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/trial-balance',
            headers: [],
            query: ['year' => '2025', 'month' => '8'],
            json: null,
            rawBody: '',
        );
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertNotNull($query->lastFrom, 'queryByPeriod must have been called');
        // Range is clamped to the calendar month (no fiscal_term row is loaded
        // because the in-memory PDO is empty, so termStart/termEnd are null
        // and the helper falls back to the calendar month).
        self::assertNotNull($query->lastFrom);
        self::assertNotNull($query->lastTo);
        self::assertSame('2025-08-01', $query->lastFrom->format('Y-m-d'));
        self::assertSame('2025-08-31', $query->lastTo->format('Y-m-d'));
        self::assertSame(self::ENTITY_ID, $query->lastEntityId);
        self::assertSame(self::FISCAL_TERM_ID, $query->lastFiscalTermId);
    }

    public function testRendersInfoMessageWhenNoRows(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::ENTITY_ID);
        $session->setSelectedFiscalTerm(self::FISCAL_TERM_ID);

        $controller = $this->buildController($session, rows: []);
        $request = new ServerRequest('GET', '/ui/trial-balance', [], [], null, '');

        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('対象期間に仕訳がありません', $response->body);
    }

    /**
     * @param list<TrialBalanceRow> $rows
     */
    private function buildController(
        SessionStore $session,
        array $rows = [],
        ?SpyTbQuery $query = null,
    ): TrialBalanceViewController {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot.\DIRECTORY_SEPARATOR.'storage'.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'ui';
        $compileDir = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'rucaro-test-smarty-'.uniqid();

        $query ??= new SpyTbQuery($rows);
        // The use case re-applies opening balances to BS rows via this
        // repository (B-3b). Mirror back whatever opening the test row was
        // built with so the two stay consistent (otherwise compute() would
        // overwrite the row's pre-baked opening with 0 on every recomputation).
        $openingsByAccount = [];
        foreach ($rows as $row) {
            if ($row->openingBalance !== '0.0000') {
                $openingsByAccount[$row->accountTitleId] = $row->openingBalance;
            }
        }
        $useCase = new QueryTrialBalanceUseCase(
            query: $query,
            snapshots: new SpyTbSnapshots(),
            clock: $clock,
            openingBalances: new MapOpeningBalanceRepoForView($openingsByAccount),
        );

        return new TrialBalanceViewController(
            useCase: $useCase,
            period: new PeriodQueryHelper(self::inMemoryPdo()),
            fiscalTerms: new \Rucaro\Support\Web\FiscalTermLookup(self::inMemoryPdo()),
            session: $session,
            csrf: new CsrfTokenManager($clock),
            flash: new FlashMessageBag(),
            view: new SmartyViewRenderer($templateDir, $compileDir),
        );
    }

    private static function inMemoryPdo(): \PDO
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, start_date TEXT, end_date TEXT)');

        return $pdo;
    }
}

/**
 * Stub TB query that returns a fixed row list and records the parameters of
 * the last queryByPeriod() call so tests can assert PeriodQueryHelper output.
 */
final class SpyTbQuery implements TrialBalanceQueryInterface
{
    public ?string $lastEntityId = null;
    public ?string $lastFiscalTermId = null;
    public ?\DateTimeImmutable $lastFrom = null;
    public ?\DateTimeImmutable $lastTo = null;

    /** @var list<TrialBalanceRow> */
    private array $rows;

    /**
     * @param list<TrialBalanceRow> $rows
     */
    public function __construct(array $rows = [])
    {
        $this->rows = $rows;
    }

    #[\Override]
    public function queryByPeriod(
        string $entityId,
        string $fiscalTermId,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
    ): TrialBalance {
        $this->lastEntityId = $entityId;
        $this->lastFiscalTermId = $fiscalTermId;
        $this->lastFrom = $from;
        $this->lastTo = $to;

        return new TrialBalance(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $from,
            toDate: $to,
            currencyCode: 'JPY',
            rows: $this->rows,
            generatedAt: new \DateTimeImmutable('2026-05-10T00:00:00+00:00', new \DateTimeZone('UTC')),
        );
    }

    #[\Override]
    public function latestSnapshotDate(string $entityId, string $fiscalTermId): ?\DateTimeImmutable
    {
        return null;
    }
}

final class SpyTbSnapshots implements TrialBalanceSnapshotRepositoryInterface
{
    #[\Override]
    public function saveAll(array $snapshots): void
    {
    }

    #[\Override]
    public function deleteByMonth(string $entityId, string $fiscalTermId, \DateTimeImmutable $monthEnd): void
    {
    }

    #[\Override]
    public function findByMonth(string $entityId, string $fiscalTermId, \DateTimeImmutable $monthEnd): array
    {
        return [];
    }
}

final class MapOpeningBalanceRepoForView implements OpeningBalanceRepositoryInterface
{
    /**
     * @param array<string, string> $byAccountId
     */
    public function __construct(private readonly array $byAccountId = [])
    {
    }

    #[\Override]
    public function findOpeningBalance(string $entityId, string $fiscalTermId, string $accountTitleId): string
    {
        return $this->byAccountId[$accountTitleId] ?? '0.0000';
    }
}
