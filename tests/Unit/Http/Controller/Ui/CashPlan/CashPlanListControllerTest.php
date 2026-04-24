<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\CashPlan;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\CashPlan\ListCashPlansUseCase;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanListController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryCashPlanRepository;
use Rucaro\Tests\Unit\Http\Controller\Ui\Budget\StubAccountTitleRepository;

#[CoversClass(CashPlanListController::class)]
final class CashPlanListControllerTest extends TestCase
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
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $controller = $this->buildController($session);
        $response = $controller->invoke(new ServerRequest('GET', '/ui/cash-plans', [], [], null, ''));
        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testRendersEmptyListOnSelectedEntity(): void
    {
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '01KPTMZZKC53RTMNPV5EK1RK0E';

        $controller = $this->buildController($session);
        $response = $controller->invoke(new ServerRequest('GET', '/ui/cash-plans', [], [], null, ''));
        self::assertSame(200, $response->status);
        self::assertStringContainsString('資金繰り計画', $response->body);
    }

    private function buildController(SessionStore $session): CashPlanListController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, fiscal_period INTEGER, start_date TEXT, end_date TEXT)');
        return new CashPlanListController(
            listPlans: new ListCashPlansUseCase(new InMemoryCashPlanRepository()),
            ctx:       new PlanningUiContext(
                new ListAccountTitlesUseCase(new StubAccountTitleRepository()),
                $pdo,
            ),
            session:   $session,
            csrf:      new CsrfTokenManager($clock),
            flash:     new FlashMessageBag(),
            view:      new SmartyViewRenderer($templateDir, $compileDir),
        );
    }
}
