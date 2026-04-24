<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Master;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Entity\CreateEntityUseCase;
use Rucaro\Application\Entity\DeleteEntityUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\UpdateEntityUseCase;
use Rucaro\Http\Controller\Ui\Master\EntityController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\Support\InMemoryEntityRepo;

#[CoversClass(EntityController::class)]
final class EntityControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testListRedirectsToLoginWhenAnonymous(): void
    {
        $controller = $this->buildController(new SessionStore());
        $response = $controller->list(new ServerRequest('GET', '/ui/masters/entities', [], [], null, ''));

        self::assertSame(303, $response->status);
        self::assertSame('/ui/login', $response->headers['Location'] ?? '');
    }

    public function testListRendersEmptyTableForAuthenticatedUser(): void
    {
        $session = new SessionStore();
        $session->setUser('01HW7K9B2QV7C8Y4ZUSER000001', 'token', 'tkid', 'Alice', 'a@example.com');

        $controller = $this->buildController($session);
        $response = $controller->list(new ServerRequest('GET', '/ui/masters/entities', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('事業主マスタ', $response->body);
    }

    public function testNewShowRendersFormWithDefaults(): void
    {
        $session = new SessionStore();
        $session->setUser('01HW7K9B2QV7C8Y4ZUSER000001', 'token', 'tkid', 'Alice', 'a@example.com');

        $controller = $this->buildController($session);
        $response = $controller->newShow(new ServerRequest('GET', '/ui/masters/entities/new', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('事業主の新規追加', $response->body);
        self::assertStringContainsString('value="JPN"', $response->body);
        self::assertStringContainsString('value="JPY"', $response->body);
    }

    private function buildController(SessionStore $session): EntityController
    {
        $clock = new FrozenClock();
        $ulids = new UlidGenerator($clock);
        $repo = new InMemoryEntityRepo();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new EntityController(
            listUseCase:   new ListEntitiesUseCase($repo),
            createUseCase: new CreateEntityUseCase($repo, $ulids, $clock),
            updateUseCase: new UpdateEntityUseCase($repo, $clock),
            deleteUseCase: new DeleteEntityUseCase($repo, $clock),
            repo:          $repo,
            session:       $session,
            csrf:          new CsrfTokenManager($clock),
            flash:         new FlashMessageBag(),
            view:          new SmartyViewRenderer($templateDir, $compileDir),
        );
    }
}
