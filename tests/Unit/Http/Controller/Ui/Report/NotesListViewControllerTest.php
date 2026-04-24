<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Report;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatementNotes\ListFsNotesUseCase;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Domain\FinancialStatementNotes\FsNotesPdfGeneratorInterface;
use Rucaro\Http\Controller\Ui\Report\NotesListViewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(NotesListViewController::class)]
final class NotesListViewControllerTest extends TestCase
{
    private const VALID_ULID = '01HW7K9B2QV7C8Y4ZFISCAL0001';

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
        $request = new ServerRequest('GET', '/ui/notes', [], [], null, '');

        $response = $controller($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testHtmlFormatReturns200(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');
        $session->setSelectedFiscalTerm(self::VALID_ULID);

        $controller = $this->buildController($session);
        $request = new ServerRequest('GET', '/ui/notes', [], [], null, '');
        $response = $controller($request);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('注記表', $response->body);
    }

    public function testPdfFormatReturnsPdfContentType(): void
    {
        $session = new SessionStore();
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');
        $session->setSelectedFiscalTerm(self::VALID_ULID);

        $controller = $this->buildController($session, emitPdf: true);
        $request = new ServerRequest(
            method: 'GET',
            path: '/ui/notes',
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
        SessionStore $session,
        bool $emitPdf = false,
    ): NotesListViewController {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new NotesListViewController(
            listNotes: new ListFsNotesUseCase(new StubFsNoteRepo()),
            pdfGenerator: new StubFsNotesGenerator($emitPdf),
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

final class StubFsNoteRepo implements FsNoteRepositoryInterface
{
    public function save(FinancialStatementNote $note): void
    {
    }

    public function findById(string $id): ?FinancialStatementNote
    {
        return null;
    }

    public function findByEntityAndTerm(
        string $entityId,
        string $fiscalTermId,
        bool $onlyActive = false,
    ): array {
        return [];
    }

    public function countByTemplateCode(
        string $entityId,
        string $fiscalTermId,
        string $templateCode,
    ): int {
        return 0;
    }

    public function delete(string $id): void
    {
    }
}

final class StubFsNotesGenerator implements FsNotesPdfGeneratorInterface
{
    public function __construct(private readonly bool $emitStub = false)
    {
    }

    public function render(array $notes, string $entityId, string $fiscalTermId): string
    {
        unset($notes, $entityId, $fiscalTermId);
        return $this->emitStub ? "%PDF-STUB\nfake notes pdf\n%%EOF" : '';
    }
}
