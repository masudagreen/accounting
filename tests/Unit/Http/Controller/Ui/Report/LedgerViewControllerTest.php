<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCaseInput;
use Rucaro\Application\Ledger\QueryLedgerUseCaseOutput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Domain\Ledger\LedgerGeneratorInterface;
use Rucaro\Domain\Ledger\LedgerQueryInterface;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Http\Controller\Ui\Report\LedgerViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(LedgerViewController::class)]
final class LedgerViewControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRedirectsToDashboardWhenNoEntitySelected(): void
    {
        $controller = $this->buildController();
        $request = new ServerRequest('GET', '/ui/ledger', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testPdfFormatReturnsApplicationPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');
        $session->setSelectedFiscalTerm('01HW7K9B2QV7C8Y4ZFISCAL0001');

        $controller = $this->buildController(session: $session, returnStubPdf: true);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/ledger',
            headers: [],
            query: ['format' => 'pdf'],
            json: null,
            rawBody: '',
        );
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertSame('application/pdf', $response->headers['Content-Type'] ?? '');
        self::assertStringStartsWith('%PDF-STUB', $response->body);
    }

    private function buildController(
        ?SessionStore $session = null,
        bool $returnStubPdf = false,
    ): LedgerViewController {
        $clock = new FrozenClock();
        $session ??= new SessionStore();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new LedgerViewController(
            queryLedger: new QueryLedgerUseCase(
                query: new StubLedgerQuery(),
                openingBalances: new StubOpeningBalances(),
                clock: $clock,
            ),
            listAccountTitles: new ListAccountTitlesUseCase(new StubAccountTitleRepo()),
            pdfGenerator: new StubLedgerGenerator($returnStubPdf),
            period: new PeriodQueryHelper(self::inMemoryPdo()),
            session: $session,
            csrf: new CsrfTokenManager($clock),
            flash: new FlashMessageBag(),
            view: new SmartyViewRenderer($templateDir, $compileDir),
        );
    }

    private static function inMemoryPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, start_date TEXT, end_date TEXT)');
        return $pdo;
    }
}

/**
 * Returns an empty Ledger; the controller only needs it to exercise the PDF branch.
 */
final class StubLedgerQuery implements LedgerQueryInterface
{
    public function query(
        string $entityId,
        string $fiscalTermId,
        ?string $accountTitleId,
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
    ): Ledger {
        return new Ledger(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $fromDate,
            toDate: $toDate,
            currencyCode: 'JPY',
            books: [],
            generatedAt: new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }
}

final class StubOpeningBalances implements OpeningBalanceRepositoryInterface
{
    public function findOpeningBalance(string $entityId, string $fiscalTermId, string $accountTitleId): string
    {
        return '0.0000';
    }
}

final class StubAccountTitleRepo implements AccountTitleRepositoryInterface
{
    public function listByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): array {
        return [];
    }

    public function countByEntity(
        string $entityId,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): int {
        return 0;
    }

    public function findById(string $id): ?AccountTitle
    {
        return null;
    }

    public function findAllByEntity(string $entityId): array
    {
        return [];
    }

    public function save(AccountTitle $title): void
    {
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
    }

    public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
    {
        return false;
    }
}

final class StubLedgerGenerator implements LedgerGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(Ledger $ledger): string
    {
        return $this->emitStub ? "%PDF-STUB\nfake pdf body\n%%EOF" : '';
    }
}
