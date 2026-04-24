<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Master;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\CreateAccountTitleUseCase;
use Rucaro\Application\AccountTitle\DeleteAccountTitleUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\UpdateAccountTitleUseCase;
use Rucaro\Http\Controller\Ui\Master\AccountTitleController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\Support\InMemoryAccountTitleRepo;

#[CoversClass(AccountTitleController::class)]
final class AccountTitleControllerTest extends TestCase
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
        $response = $controller->list(new ServerRequest('GET', '/ui/masters/account-titles', [], [], null, ''));

        self::assertSame(303, $response->status);
        self::assertSame('/ui/login', $response->headers['Location'] ?? '');
    }

    public function testListRedirectsToDashboardWhenNoEntitySelected(): void
    {
        $session = new SessionStore();
        $session->setUser('01HW7K9B2QV7C8Y4ZUSER000001', 'token', 'tkid', 'Alice', 'a@example.com');

        $controller = $this->buildController($session);
        $response = $controller->list(new ServerRequest('GET', '/ui/masters/account-titles', [], [], null, ''));

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testNewShowRendersEmptyForm(): void
    {
        $session = $this->authedSession();
        $controller = $this->buildController($session);

        $response = $controller->newShow(new ServerRequest('GET', '/ui/masters/account-titles/new', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('勘定科目の新規追加', $response->body);
        self::assertStringContainsString('name="code"', $response->body);
    }

    public function testDeleteWithInvalidCsrfRedirectsWithError(): void
    {
        $session = $this->authedSession();
        $controller = $this->buildController($session);

        $response = $controller->delete(
            new ServerRequest('POST', '/ui/masters/account-titles/x/delete', [], [], null, '_csrf=bad'),
            'some-id',
        );

        self::assertSame(303, $response->status);
        self::assertSame('/ui/masters/account-titles', $response->headers['Location'] ?? '');
    }

    private function authedSession(): SessionStore
    {
        $session = new SessionStore();
        $session->setUser('01HW7K9B2QV7C8Y4ZUSER000001', 'token', 'tkid', 'Alice', 'a@example.com');
        $session->setSelectedEntity('01HW7K9B2QV7C8Y4ZENTITY0001');
        return $session;
    }

    private function buildController(SessionStore $session): AccountTitleController
    {
        $clock = new FrozenClock();
        $ulids = new UlidGenerator($clock);
        $repo = new InMemoryAccountTitleRepo();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();

        return new AccountTitleController(
            listUseCase:   new ListAccountTitlesUseCase($repo),
            createUseCase: new CreateAccountTitleUseCase($repo, $ulids, $clock),
            updateUseCase: new UpdateAccountTitleUseCase($repo, $clock),
            deleteUseCase: new DeleteAccountTitleUseCase($repo, $clock),
            repo:          $repo,
            session:       $session,
            csrf:          new CsrfTokenManager($clock),
            flash:         new FlashMessageBag(),
            view:          new SmartyViewRenderer($templateDir, $compileDir),
        );
    }
}
