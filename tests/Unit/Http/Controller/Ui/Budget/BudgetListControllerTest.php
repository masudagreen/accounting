<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Budget;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\Budget\ListBudgetsUseCase;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Http\Controller\Ui\Budget\BudgetListController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;

#[CoversClass(BudgetListController::class)]
final class BudgetListControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRedirectsToLoginWhenNotAuthenticated(): void
    {
        $controller = $this->buildController(new SessionStore());
        $response = $controller->invoke(new ServerRequest('GET', '/ui/budgets', [], [], null, ''));
        self::assertSame(303, $response->status);
        self::assertSame('/ui/login', $response->headers['Location'] ?? '');
    }

    public function testRendersWithEmptyListForSelectedEntity(): void
    {
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '01KPTMZZKC53RTMNPV5EK1RK0E';

        $controller = $this->buildController($session);
        $response = $controller->invoke(new ServerRequest('GET', '/ui/budgets', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('予算', $response->body);
    }

    private function buildController(SessionStore $session): BudgetListController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        $pdo = self::inMemoryPdo();
        return new BudgetListController(
            listBudgets: new ListBudgetsUseCase(new InMemoryBudgetRepository()),
            ctx:         new PlanningUiContext(
                new ListAccountTitlesUseCase(new StubAccountTitleRepository()),
                $pdo,
            ),
            session:     $session,
            csrf:        new CsrfTokenManager($clock),
            flash:       new FlashMessageBag(),
            view:        new SmartyViewRenderer($templateDir, $compileDir),
        );
    }

    private static function inMemoryPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, fiscal_period INTEGER, start_date TEXT, end_date TEXT)');
        return $pdo;
    }
}

/** @internal */
final class StubAccountTitleRepository implements AccountTitleRepositoryInterface
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
