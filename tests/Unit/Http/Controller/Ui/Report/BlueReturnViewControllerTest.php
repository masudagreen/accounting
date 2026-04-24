<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BlueReturn\GetBlueReturnUseCase;
use Rucaro\Application\BlueReturn\ListBlueReturnsUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnPdfGeneratorInterface;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Http\Controller\Ui\Report\BlueReturnViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(BlueReturnViewController::class)]
final class BlueReturnViewControllerTest extends TestCase
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
        $request = new ServerRequest('GET', '/ui/blue-return', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testHtmlFormatReturns200WhenNoForm(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');

        $controller = $this->buildController($session);
        $request = new ServerRequest('GET', '/ui/blue-return', [], [], null, '');
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('青色申告', $response->body);
    }

    public function testPdfFormatReturnsHtmlWhenNoFormExists(): void
    {
        // When the entity has no Blue Return form, the PDF branch is skipped
        // and the page renders the "no form" message as HTML 200.
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');

        $controller = $this->buildController($session, emitPdf: true);
        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/blue-return',
            headers: [],
            query: ['format' => 'pdf'],
            json: null,
            rawBody: '',
        );
        $response = $controller($request);

        self::assertSame(200, $response->status);
        // No form -> still HTML (the PDF branch requires a form).
        self::assertStringContainsString('青色申告', $response->body);
    }

    private function buildController(
        SessionStore $session,
        bool $emitPdf = false,
    ): BlueReturnViewController {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        $repo = new StubBlueReturnRepo();
        return new BlueReturnViewController(
            getBlueReturn: new GetBlueReturnUseCase($repo),
            listBlueReturns: new ListBlueReturnsUseCase($repo),
            pdfGenerator: new StubBlueReturnGenerator($emitPdf),
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

final class StubBlueReturnRepo implements BlueReturnRepositoryInterface
{
    public function save(BlueReturnForm $form): void
    {
    }

    public function findById(string $id): ?BlueReturnForm
    {
        return null;
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): ?BlueReturnForm
    {
        return null;
    }

    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        bool $includeDeleted = false,
    ): array {
        return [];
    }

    public function delete(string $id): void
    {
    }
}

final class StubBlueReturnGenerator implements BlueReturnPdfGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(BlueReturnForm $form): string
    {
        unset($form);
        return $this->emitStub ? "%PDF-STUB\nfake blue return pdf\n%%EOF" : '';
    }
}
