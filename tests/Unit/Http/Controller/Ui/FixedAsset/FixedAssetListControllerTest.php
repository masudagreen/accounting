<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FixedAsset\ListFixedAssetsUseCase;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetListController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryFixedAssetRepository;

#[CoversClass(FixedAssetListController::class)]
final class FixedAssetListControllerTest extends TestCase
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
        $controller = $this->buildController(new InMemoryFixedAssetRepository(), $session);
        $request = new ServerRequest('GET', '/ui/fixed-assets', [], [], null, '');

        $response = $controller->invoke($request);

        self::assertSame(303, $response->status);
        self::assertSame('/ui/dashboard', $response->headers['Location'] ?? '');
    }

    public function testRendersItemsForSelectedEntity(): void
    {
        $entityId = '01KPTMZZKC53RTMNPV5EK1RK0E';
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = $entityId;

        $repo = new InMemoryFixedAssetRepository();
        $repo->save($this->makeAsset($entityId, 'A001', 'Office Chair'));

        $controller = $this->buildController($repo, $session);
        $response = $controller->invoke(new ServerRequest('GET', '/ui/fixed-assets', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('固定資産', $response->body);
        self::assertStringContainsString('Office Chair', $response->body);
    }

    private function buildController(InMemoryFixedAssetRepository $repo, SessionStore $session): FixedAssetListController
    {
        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        return new FixedAssetListController(
            listAssets: new ListFixedAssetsUseCase($repo),
            session:    $session,
            csrf:       new CsrfTokenManager($clock),
            flash:      new FlashMessageBag(),
            view:       new SmartyViewRenderer($templateDir, $compileDir),
        );
    }

    private function makeAsset(string $entityId, string $code, string $name): FixedAsset
    {
        $date = new DateTimeImmutable('2025-04-01', new DateTimeZone('UTC'));
        return new FixedAsset(
            id: '01HW000000000000000000ASSET',
            entityId: $entityId,
            assetCode: $code,
            assetName: $name,
            categoryCode: 'machinery',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: $date,
            serviceStartDate: $date,
            disposalDate: null,
            acquisitionCost: '100000.0000',
            residualValue: '1.0000',
            usefulLifeYears: 5,
            method: DepreciationMethod::StraightLine,
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: '01HW000000000000000000USER',
            createdAt: $date,
            updatedAt: $date,
            deletedAt: null,
        );
    }
}
