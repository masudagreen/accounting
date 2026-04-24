<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\FixedAsset;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetNewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFixedAssetRepository;

#[CoversClass(FixedAssetNewController::class)]
final class FixedAssetNewControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testMethodOptionsExposesEveryDepreciationMethod(): void
    {
        $options = FixedAssetNewController::methodOptions();
        $values = array_map(static fn (array $o): string => $o['value'], $options);
        self::assertContains('straight_line', $values);
        self::assertContains('declining_balance', $values);
        self::assertContains('none', $values);
    }

    public function testRendersBlankForm(): void
    {
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '01KPTMZZKC53RTMNPV5EK1RK0E';

        $controller = $this->buildController($session);
        $response = $controller->show(new ServerRequest('GET', '/ui/fixed-assets/new', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('新規固定資産', $response->body);
        self::assertStringContainsString('資産コード', $response->body);
    }

    private function buildController(SessionStore $session): FixedAssetNewController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        return new FixedAssetNewController(
            createAsset: new CreateFixedAssetUseCase(
                new InMemoryFixedAssetRepository(),
                new UlidGenerator($clock),
                $clock,
            ),
            session:     $session,
            csrf:        new CsrfTokenManager($clock),
            flash:       new FlashMessageBag(),
            view:        new SmartyViewRenderer($templateDir, $compileDir),
        );
    }
}
