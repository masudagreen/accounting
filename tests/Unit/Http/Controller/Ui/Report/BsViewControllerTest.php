<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Http\Controller\Ui\Report\BsViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(BsViewController::class)]
final class BsViewControllerTest extends TestCase
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
        $request = new ServerRequest('GET', '/ui/bs', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testPdfFormatReturnsPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');
        $session->setSelectedFiscalTerm('01HW7K9B2QV7C8Y4ZFISCAL0001');

        $controller = $this->buildController($session, emitPdf: true);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/bs',
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

    private function buildController(SessionStore $session, bool $emitPdf = false): BsViewController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new BsViewController(
            useCase: new GenerateFinancialStatementUseCase(
                trialBalance: new QueryTrialBalanceUseCase(
                    query: new StubTrialBalanceQuery(),
                    snapshots: new StubTrialBalanceSnapshots(),
                    clock: $clock,
                ),
                accounts: new StubAccountTitleRepoForPl(),
                clock: $clock,
            ),
            pdfGenerator: new StubFsGenerator($emitPdf),
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
