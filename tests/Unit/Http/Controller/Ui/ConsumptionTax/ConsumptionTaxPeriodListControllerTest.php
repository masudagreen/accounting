<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\ConsumptionTax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\ConsumptionTax\ListConsumptionTaxPeriodsUseCase;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodListController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodNewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryConsumptionTaxPeriodRepository;

#[CoversClass(ConsumptionTaxPeriodListController::class)]
final class ConsumptionTaxPeriodListControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRendersEmptyListForSelectedEntity(): void
    {
        $session = new SessionStore();
        $_SESSION[SessionStore::KEY_USER_ID] = '01HW000000000000000000USER';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '01KPTMZZKC53RTMNPV5EK1RK0E';

        $clock = new FrozenClock();
        $repoRoot = dirname(__DIR__, 6);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rucaro-test-smarty-' . uniqid();
        $controller = new ConsumptionTaxPeriodListController(
            listPeriods: new ListConsumptionTaxPeriodsUseCase(new InMemoryConsumptionTaxPeriodRepository()),
            session:     $session,
            csrf:        new CsrfTokenManager($clock),
            flash:       new FlashMessageBag(),
            view:        new SmartyViewRenderer($templateDir, $compileDir),
        );

        $response = $controller->invoke(new ServerRequest('GET', '/ui/consumption-tax/periods', [], [], null, ''));

        self::assertSame(200, $response->status);
        self::assertStringContainsString('消費税申告期間', $response->body);
    }

    public function testMethodOptionsContainsAllCases(): void
    {
        $opts = ConsumptionTaxPeriodNewController::methodOptions();
        $values = array_map(static fn (array $o): string => $o['value'], $opts);
        self::assertContains('principle', $values);
        self::assertContains('simplified', $values);
        self::assertContains('two_percent', $values);
    }
}
