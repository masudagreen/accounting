<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\FinancialStatement\Multi\FinancialStatementProviderInterface;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadata;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadataRepositoryInterface;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Http\Controller\Ui\Report\MultiPeriodFsViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodFinancialStatementGeneratorInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(MultiPeriodFsViewController::class)]
final class MultiPeriodFsViewControllerTest extends TestCase
{
    private const VALID_ULID = '01KPTM4YRB15BEXCNHE0WHGNV5';
    private const VALID_ENTITY = '01KPTM4YRBCKZZC6HKWYEG9H73';

    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRedirectsWhenNoEntitySelected(): void
    {
        $controller = $this->buildController(new SessionStore());
        $request = new ServerRequest('GET', '/ui/fs/multi', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testHtmlFormatReturns200WithTermIds(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::VALID_ENTITY);

        $controller = $this->buildController($session);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/fs/multi',
            headers: [],
            query: ['termIds' => self::VALID_ULID, 'kind' => 'ALL'],
            json: null,
            rawBody: '',
        );
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('複数期比較決算書', $response->body);
    }

    public function testPdfFormatReturnsPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity(self::VALID_ENTITY);

        $controller = $this->buildController($session, emitPdf: true);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/fs/multi',
            headers: [],
            query: ['termIds' => self::VALID_ULID, 'format' => 'pdf'],
            json: null,
            rawBody: '',
        );
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertSame('application/pdf', $response->headers['Content-Type'] ?? '');
        self::assertStringStartsWith('%PDF-STUB', $response->body);
    }

    private function buildController(
        SessionStore $session,
        bool $emitPdf = false,
    ): MultiPeriodFsViewController {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new MultiPeriodFsViewController(
            useCase: new GenerateMultiPeriodFinancialStatementUseCase(
                provider: new StubFsProvider(),
                fiscalTerms: new StubFiscalTermMetadataRepo(),
                clock: $clock,
            ),
            pdfGenerator: new StubMultiFsGenerator($emitPdf),
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

final class StubFsProvider implements FinancialStatementProviderInterface
{
    public function provide(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        $utc = new DateTimeZone('UTC');
        return new FinancialStatement(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            kind: $input->kind,
            fromDate: $input->fromDate,
            toDate: $input->asOf,
            currencyCode: $input->currencyCode,
            bs: [],
            pl: [],
            cs: [],
            totals: [],
            generatedAt: new DateTimeImmutable('now', $utc),
        );
    }
}

final class StubFiscalTermMetadataRepo implements FiscalTermMetadataRepositoryInterface
{
    public function findByIds(array $ids): array
    {
        $utc = new DateTimeZone('UTC');
        $out = [];
        foreach ($ids as $id) {
            $out[] = new FiscalTermMetadata(
                id: $id,
                label: '第 1 期',
                startDate: new DateTimeImmutable('2025-01-01', $utc),
                endDate: new DateTimeImmutable('2025-12-31', $utc),
            );
        }
        return $out;
    }
}

final class StubMultiFsGenerator implements MultiPeriodFinancialStatementGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(MultiPeriodFinancialStatement $statement): string
    {
        return $this->emitStub ? "%PDF-STUB\nfake multi pdf\n%%EOF" : '';
    }

    public function renderHtml(MultiPeriodFinancialStatement $statement): string
    {
        return '';
    }
}
