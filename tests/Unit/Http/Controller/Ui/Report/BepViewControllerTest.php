<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
use Rucaro\Domain\BreakEvenPoint\Service\BreakEvenPointCalculator;
use Rucaro\Http\Controller\Ui\Report\BepViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(BepViewController::class)]
final class BepViewControllerTest extends TestCase
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
        $request = new ServerRequest('GET', '/ui/bep', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testPdfFormatReturnsPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01KPTM4YRBCKZZC6HKWYEG9H73');
        $session->setSelectedFiscalTerm('01KPTM4YRB15BEXCNHE0WHGNV5');

        $controller = $this->buildController($session, emitPdf: true);

        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/bep',
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

    public function testHtmlFormatReturns200(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01KPTM4YRBCKZZC6HKWYEG9H73');
        $session->setSelectedFiscalTerm('01KPTM4YRB15BEXCNHE0WHGNV5');

        $controller = $this->buildController($session);

        $request = new ServerRequest('GET', '/ui/bep', [], [], null, '');
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('損益分岐点', $response->body);
    }

    private function buildController(SessionStore $session, bool $emitPdf = false): BepViewController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new BepViewController(
            useCase: new AnalyzeBreakEvenPointUseCase(
                trialBalance: new QueryTrialBalanceUseCase(
                    query: new StubTrialBalanceQuery(),
                    snapshots: new StubTrialBalanceSnapshots(),
                    clock: $clock,
                ),
                classifications: new StubCvpClassificationRepo(),
                calculator: new BreakEvenPointCalculator(),
                clock: $clock,
            ),
            pdfGenerator: new StubBepGenerator($emitPdf),
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

final class StubCvpClassificationRepo implements AccountTitleCvpClassificationRepositoryInterface
{
    public function findAllByEntity(string $entityId): array
    {
        return [];
    }

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleCvpClassification
    {
        return null;
    }

    public function save(AccountTitleCvpClassification $classification): void
    {
    }

    public function saveMany(array $classifications): void
    {
    }

    public function delete(string $entityId, string $accountTitleId): void
    {
    }
}

final class StubBepGenerator implements BreakEvenPointPdfGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(BreakEvenPointAnalysis $analysis): string
    {
        unset($analysis);
        return $this->emitStub ? "%PDF-STUB\nfake bep pdf\n%%EOF" : '';
    }
}
