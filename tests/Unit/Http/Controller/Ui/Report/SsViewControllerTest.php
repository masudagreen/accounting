<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityUseCase;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquityPdfGeneratorInterface;
use Rucaro\Http\Controller\Ui\Report\SsViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(SsViewController::class)]
final class SsViewControllerTest extends TestCase
{
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
        $request = new ServerRequest('GET', '/ui/ss', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testHtmlFormatReturns200(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01KPTM4YRBCKZZC6HKWYEG9H73');
        $session->setSelectedFiscalTerm('01KPTM4YRB15BEXCNHE0WHGNV5');

        $controller = $this->buildController($session);
        $request = new ServerRequest('GET', '/ui/ss', [], [], null, '');
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('株主資本', $response->body);
    }

    public function testPdfFormatReturnsPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01KPTM4YRBCKZZC6HKWYEG9H73');
        $session->setSelectedFiscalTerm('01KPTM4YRB15BEXCNHE0WHGNV5');

        $controller = $this->buildController($session, emitPdf: true);
        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/ss',
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

    private function buildController(SessionStore $session, bool $emitPdf = false): SsViewController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new SsViewController(
            useCase: new GenerateStatementOfChangesInEquityUseCase(
                repo: new StubSsAdjustmentRepo(),
                builder: new StatementOfChangesInEquityBuilder(),
                clock: $clock,
            ),
            pdfGenerator: new StubSsGenerator($emitPdf),
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

final class StubSsAdjustmentRepo implements SsManualAdjustmentRepositoryInterface
{
    public function save(SsManualAdjustment $adjustment): void
    {
    }

    public function findById(string $id): ?SsManualAdjustment
    {
        return null;
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array
    {
        return [];
    }

    public function delete(string $id): void
    {
    }
}

final class StubSsGenerator implements StatementOfChangesInEquityPdfGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(StatementOfChangesInEquity $statement): string
    {
        unset($statement);
        return $this->emitStub ? "%PDF-STUB\nfake ss pdf\n%%EOF" : '';
    }
}
