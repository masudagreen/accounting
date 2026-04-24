<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\StatementOfChangesInEquity;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\StatementOfChangesInEquity\ListSsAdjustmentsUseCase;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentListController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentNewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemorySsManualAdjustmentRepository;
use Rucaro\Tests\Unit\Http\Controller\Ui\Budget\StubAccountTitleRepository;

#[CoversClass(SsAdjustmentListController::class)]
final class SsAdjustmentListControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRendersEmptyList(): void
    {
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '01KPTMZZKC53RTMNPV5EK1RK0E';

        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, fiscal_period INTEGER, start_date TEXT, end_date TEXT)');
        $controller = new SsAdjustmentListController(
            listAdjustments: new ListSsAdjustmentsUseCase(new InMemorySsManualAdjustmentRepository()),
            ctx:             new PlanningUiContext(
                new ListAccountTitlesUseCase(new StubAccountTitleRepository()),
                $pdo,
            ),
            clock:           $clock,
            session:         $session,
            csrf:            new CsrfTokenManager($clock),
            flash:           new FlashMessageBag(),
            view:            new SmartyViewRenderer($templateDir, $compileDir),
        );

        $response = $controller->invoke(new ServerRequest('GET', '/ui/ss-adjustments', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('純資産変動調整', $response->body);
    }

    public function testSectionAndChangeOptionsExist(): void
    {
        $sections = SsAdjustmentNewController::sectionOptions();
        $changes  = SsAdjustmentNewController::changeOptions();
        self::assertNotEmpty($sections);
        self::assertNotEmpty($changes);
    }
}
