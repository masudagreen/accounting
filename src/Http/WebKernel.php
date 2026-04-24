<?php

declare(strict_types=1);

namespace Rucaro\Http;

use Psr\Container\ContainerInterface;
use Rucaro\Http\Controller\Ui\DashboardController;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\Journal\JournalDeleteController;
use Rucaro\Http\Controller\Ui\Journal\JournalEditController;
use Rucaro\Http\Controller\Ui\Journal\JournalListController;
use Rucaro\Http\Controller\Ui\Journal\JournalNewController;
use Rucaro\Http\Controller\Ui\Journal\JournalShowController;
use Rucaro\Http\Controller\Ui\LoginController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Master\AccountTitleController as MasterAccountTitleController;
use Rucaro\Http\Controller\Ui\Master\EntityController as MasterEntityController;
use Rucaro\Http\Controller\Ui\Master\FiscalTermController as MasterFiscalTermController;
use Rucaro\Http\Controller\Ui\Master\SubAccountTitleController as MasterSubAccountTitleController;
use Rucaro\Http\Controller\Ui\Budget\BudgetLifecycleController;
use Rucaro\Http\Controller\Ui\Budget\BudgetListController;
use Rucaro\Http\Controller\Ui\Budget\BudgetNewController;
use Rucaro\Http\Controller\Ui\Budget\BudgetShowController;
use Rucaro\Http\Controller\Ui\Budget\BudgetVarianceController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanDeleteController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanListController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanNewController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanShowController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\AccountDefaultsController as CtAccountDefaultsController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxCalculateController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodListController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodNewController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodShowController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\InvoiceRegistrationController as CtInvoiceRegistrationController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetDepreciateController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetDisposeController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetEditController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetListController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetNewController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetShowController;
use Rucaro\Http\Controller\Ui\Report\BepViewController;
use Rucaro\Http\Controller\Ui\Report\BlueReturnViewController;
use Rucaro\Http\Controller\Ui\Report\BsViewController;
use Rucaro\Http\Controller\Ui\Report\CsViewController;
use Rucaro\Http\Controller\Ui\Report\LedgerViewController;
use Rucaro\Http\Controller\Ui\Report\MultiPeriodFsViewController;
use Rucaro\Http\Controller\Ui\Report\NotesListViewController;
use Rucaro\Http\Controller\Ui\Report\PlViewController;
use Rucaro\Http\Controller\Ui\Report\SsViewController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentDeleteController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentEditController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentListController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentNewController;
use Rucaro\Http\Middleware\AuthenticateSession;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Support\Web\SessionStore;

/**
 * Web UI kernel mounted at `/ui/*`. Sibling of {@see ApiKernel}; the two
 * never share routes or middleware so the REST API contract stays byte-stable
 * while the UI evolves.
 *
 * Route table is intentionally hand-rolled (no FastRoute) to keep the UI
 * surface self-contained. Route paths may contain `{name}` placeholders
 * which match one non-slash segment and are forwarded to the controller as
 * named extra arguments after the {@see ServerRequest}. Handlers of the
 * form `Class@method` invoke that method; plain class names fall back to
 * `__invoke` to keep Phase 7-1 controllers working unchanged.
 *
 * Authentication flow:
 *   - `/ui/login`  and `/ui/logout` are publicly reachable.
 *   - Every other `/ui/*` route requires a valid session; {@see AuthenticateSession}
 *     validates the stored Bearer plaintext against the API token repo.
 */
final class WebKernel
{
    /** @var list<array{method: string, path: string, handler: string, public: bool}> */
    private array $routes;

    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {
        $this->routes = [
            ['method' => 'GET',  'path' => '/ui',                        'handler' => LoginController::class,         'public' => true],
            ['method' => 'GET',  'path' => '/ui/',                       'handler' => LoginController::class,         'public' => true],
            ['method' => 'GET',  'path' => '/ui/login',                  'handler' => LoginController::class,         'public' => true],
            ['method' => 'POST', 'path' => '/ui/login',                  'handler' => LoginController::class,         'public' => true],
            ['method' => 'POST', 'path' => '/ui/logout',                 'handler' => LogoutController::class,        'public' => true],
            ['method' => 'GET',  'path' => '/ui/dashboard',              'handler' => DashboardController::class,     'public' => false],
            ['method' => 'POST', 'path' => '/ui/entity/switch',          'handler' => EntitySwitchController::class,  'public' => false],
            // --- Phase 7-2: Journal CRUD ---
            ['method' => 'GET',  'path' => '/ui/journals',               'handler' => JournalListController::class   . '@invoke', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/journals/new',           'handler' => JournalNewController::class    . '@show',   'public' => false],
            ['method' => 'POST', 'path' => '/ui/journals/new',           'handler' => JournalNewController::class    . '@submit', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/journals/{id}/delete',   'handler' => JournalDeleteController::class . '@show',   'public' => false],
            ['method' => 'POST', 'path' => '/ui/journals/{id}/delete',   'handler' => JournalDeleteController::class . '@submit', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/journals/{id}',          'handler' => JournalShowController::class   . '@invoke', 'public' => false],
            ['method' => 'POST', 'path' => '/ui/journals/{id}',          'handler' => JournalEditController::class   . '@submit', 'public' => false],
            // --- Phase 7-3: Report views (Ledger / PL / BS) ---
            ['method' => 'GET',  'path' => '/ui/ledger',                 'handler' => LedgerViewController::class,   'public' => false],
            ['method' => 'GET',  'path' => '/ui/pl',                     'handler' => PlViewController::class,       'public' => false],
            ['method' => 'GET',  'path' => '/ui/bs',                     'handler' => BsViewController::class,       'public' => false],
            // --- Phase 7-4-B: CS / Multi-period FS / BEP / Blue return / SS / Notes ---
            ['method' => 'GET',  'path' => '/ui/cs',                     'handler' => CsViewController::class,            'public' => false],
            ['method' => 'GET',  'path' => '/ui/fs/multi',               'handler' => MultiPeriodFsViewController::class, 'public' => false],
            ['method' => 'GET',  'path' => '/ui/bep',                    'handler' => BepViewController::class,           'public' => false],
            ['method' => 'GET',  'path' => '/ui/blue-return',            'handler' => BlueReturnViewController::class,    'public' => false],
            ['method' => 'GET',  'path' => '/ui/ss',                     'handler' => SsViewController::class,            'public' => false],
            ['method' => 'GET',  'path' => '/ui/notes',                  'handler' => NotesListViewController::class,     'public' => false],
            // --- Phase 7-4-A: Master CRUD UI ---
            ['method' => 'GET',  'path' => '/ui/masters/account-titles',                  'handler' => MasterAccountTitleController::class    . '@list',        'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/account-titles/new',              'handler' => MasterAccountTitleController::class    . '@newShow',     'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/account-titles/new',              'handler' => MasterAccountTitleController::class    . '@newSubmit',   'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/account-titles/{id}/delete',      'handler' => MasterAccountTitleController::class    . '@deleteShow',  'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/account-titles/{id}/delete',      'handler' => MasterAccountTitleController::class    . '@delete',      'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/account-titles/{id}',             'handler' => MasterAccountTitleController::class    . '@editShow',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/account-titles/{id}',             'handler' => MasterAccountTitleController::class    . '@editSubmit',  'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/sub-account-titles',              'handler' => MasterSubAccountTitleController::class . '@list',        'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/sub-account-titles/new',          'handler' => MasterSubAccountTitleController::class . '@newShow',     'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/sub-account-titles/new',          'handler' => MasterSubAccountTitleController::class . '@newSubmit',   'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/sub-account-titles/{id}/delete',  'handler' => MasterSubAccountTitleController::class . '@deleteShow',  'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/sub-account-titles/{id}/delete',  'handler' => MasterSubAccountTitleController::class . '@delete',      'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/sub-account-titles/{id}',         'handler' => MasterSubAccountTitleController::class . '@editShow',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/sub-account-titles/{id}',         'handler' => MasterSubAccountTitleController::class . '@editSubmit',  'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/entities',                        'handler' => MasterEntityController::class          . '@list',        'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/entities/new',                    'handler' => MasterEntityController::class          . '@newShow',     'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/entities/new',                    'handler' => MasterEntityController::class          . '@newSubmit',   'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/entities/{id}/delete',            'handler' => MasterEntityController::class          . '@deleteShow',  'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/entities/{id}/delete',            'handler' => MasterEntityController::class          . '@delete',      'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/entities/{id}',                   'handler' => MasterEntityController::class          . '@editShow',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/entities/{id}',                   'handler' => MasterEntityController::class          . '@editSubmit',  'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/fiscal-terms',                    'handler' => MasterFiscalTermController::class      . '@list',        'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/fiscal-terms/new',                'handler' => MasterFiscalTermController::class      . '@newShow',     'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/fiscal-terms/new',                'handler' => MasterFiscalTermController::class      . '@newSubmit',   'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/fiscal-terms/{id}/delete',        'handler' => MasterFiscalTermController::class      . '@deleteShow',  'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/fiscal-terms/{id}/delete',        'handler' => MasterFiscalTermController::class      . '@delete',      'public' => false],
            ['method' => 'GET',  'path' => '/ui/masters/fiscal-terms/{id}',               'handler' => MasterFiscalTermController::class      . '@editShow',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/masters/fiscal-terms/{id}',               'handler' => MasterFiscalTermController::class      . '@editSubmit',  'public' => false],
            // --- Phase 7-4-C: Planning CRUD (固定資産 / 予算 / 資金繰り / 消費税 / 純資産変動調整) ---
            ['method' => 'GET',  'path' => '/ui/fixed-assets',                            'handler' => FixedAssetListController::class        . '@invoke',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/fixed-assets/new',                        'handler' => FixedAssetNewController::class         . '@show',         'public' => false],
            ['method' => 'POST', 'path' => '/ui/fixed-assets/new',                        'handler' => FixedAssetNewController::class         . '@submit',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/fixed-assets/{id}',                       'handler' => FixedAssetShowController::class        . '@invoke',       'public' => false],
            ['method' => 'POST', 'path' => '/ui/fixed-assets/{id}',                       'handler' => FixedAssetEditController::class        . '@submit',       'public' => false],
            ['method' => 'POST', 'path' => '/ui/fixed-assets/{id}/dispose',               'handler' => FixedAssetDisposeController::class     . '@submit',       'public' => false],
            ['method' => 'POST', 'path' => '/ui/fixed-assets/{id}/depreciate',            'handler' => FixedAssetDepreciateController::class  . '@submit',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/budgets',                                 'handler' => BudgetListController::class            . '@invoke',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/budgets/new',                             'handler' => BudgetNewController::class             . '@show',         'public' => false],
            ['method' => 'POST', 'path' => '/ui/budgets/new',                             'handler' => BudgetNewController::class             . '@submit',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/budgets/{id}',                            'handler' => BudgetShowController::class            . '@invoke',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/budgets/{id}/variance',                   'handler' => BudgetVarianceController::class,                          'public' => false],
            ['method' => 'POST', 'path' => '/ui/budgets/{id}/approve',                    'handler' => BudgetLifecycleController::class       . '@approveAction','public' => false],
            ['method' => 'POST', 'path' => '/ui/budgets/{id}/lock',                       'handler' => BudgetLifecycleController::class       . '@lockAction',   'public' => false],
            ['method' => 'POST', 'path' => '/ui/budgets/{id}/delete',                     'handler' => BudgetLifecycleController::class       . '@deleteAction', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/cash-plans',                              'handler' => CashPlanListController::class          . '@invoke',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/cash-plans/new',                          'handler' => CashPlanNewController::class           . '@show',         'public' => false],
            ['method' => 'POST', 'path' => '/ui/cash-plans/new',                          'handler' => CashPlanNewController::class           . '@submit',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/cash-plans/{id}',                         'handler' => CashPlanShowController::class          . '@invoke',       'public' => false],
            ['method' => 'POST', 'path' => '/ui/cash-plans/{id}/delete',                  'handler' => CashPlanDeleteController::class        . '@submit',       'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/periods',                 'handler' => ConsumptionTaxPeriodListController::class . '@invoke',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/periods/new',             'handler' => ConsumptionTaxPeriodNewController::class  . '@show',      'public' => false],
            ['method' => 'POST', 'path' => '/ui/consumption-tax/periods/new',             'handler' => ConsumptionTaxPeriodNewController::class  . '@submit',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/periods/{id}',            'handler' => ConsumptionTaxPeriodShowController::class . '@invoke',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/consumption-tax/periods/{id}/calculate',  'handler' => ConsumptionTaxCalculateController::class  . '@calculateAction', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/periods/{id}/report',     'handler' => ConsumptionTaxCalculateController::class  . '@reportAction', 'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/account-defaults',        'handler' => CtAccountDefaultsController::class        . '@show',      'public' => false],
            ['method' => 'POST', 'path' => '/ui/consumption-tax/account-defaults',        'handler' => CtAccountDefaultsController::class        . '@submit',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/consumption-tax/invoice-registrations',   'handler' => CtInvoiceRegistrationController::class    . '@show',      'public' => false],
            ['method' => 'POST', 'path' => '/ui/consumption-tax/invoice-registrations',   'handler' => CtInvoiceRegistrationController::class    . '@submit',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/ss-adjustments',                          'handler' => SsAdjustmentListController::class         . '@invoke',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/ss-adjustments/new',                      'handler' => SsAdjustmentNewController::class          . '@show',      'public' => false],
            ['method' => 'POST', 'path' => '/ui/ss-adjustments/new',                      'handler' => SsAdjustmentNewController::class          . '@submit',    'public' => false],
            ['method' => 'GET',  'path' => '/ui/ss-adjustments/{id}',                     'handler' => SsAdjustmentEditController::class         . '@show',      'public' => false],
            ['method' => 'POST', 'path' => '/ui/ss-adjustments/{id}',                     'handler' => SsAdjustmentEditController::class         . '@submit',    'public' => false],
            ['method' => 'POST', 'path' => '/ui/ss-adjustments/{id}/delete',              'handler' => SsAdjustmentDeleteController::class       . '@submit',    'public' => false],
        ];
    }

    public function handle(?ServerRequest $request = null): HtmlResponse
    {
        $request ??= ServerRequest::fromGlobals();

        if ($this->container === null) {
            return HtmlResponse::of(
                503,
                '<!doctype html><meta charset="utf-8"><title>503</title>'
                . '<h1>Service Unavailable</h1>'
                . '<p>DI container is not wired for the Web UI kernel.</p>',
            );
        }

        $match = $this->matchRoute($request->method, $request->path);
        if ($match === null) {
            return $this->notFound();
        }
        $route = $match['route'];
        $params = $match['params'];

        if (!$route['public']) {
            $auth = $this->authSession();
            if ($auth === null || $auth->authenticate() === null) {
                return HtmlResponse::redirect('/ui/login');
            }
        }

        [$class, $method] = self::splitHandler($route['handler']);
        /** @var object $controller */
        $controller = $this->container->get($class);

        if ($method === '__invoke') {
            if (!is_callable($controller)) {
                return HtmlResponse::of(500, '<!doctype html><meta charset="utf-8"><h1>500</h1><p>Controller not callable.</p>');
            }
            /** @var HtmlResponse $response */
            $response = $controller($request, ...array_values($params));
            return $response;
        }

        if (!method_exists($controller, $method)) {
            return HtmlResponse::of(500, '<!doctype html><meta charset="utf-8"><h1>500</h1><p>Controller method missing: ' . htmlspecialchars($method) . '</p>');
        }
        /** @var callable $callable */
        $callable = [$controller, $method];
        /** @var HtmlResponse $response */
        $response = $callable($request, ...array_values($params));
        return $response;
    }

    /**
     * @return array{route: array{method: string, path: string, handler: string, public: bool}, params: array<string, string>}|null
     */
    private function matchRoute(string $method, string $path): ?array
    {
        $normalized = self::normalizePath($path);
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $routePath = self::normalizePath($route['path']);
            if (str_contains($routePath, '{')) {
                $params = self::matchTemplate($routePath, $normalized);
                if ($params !== null) {
                    return ['route' => $route, 'params' => $params];
                }
                continue;
            }
            if ($routePath === $normalized) {
                return ['route' => $route, 'params' => []];
            }
        }
        return null;
    }

    private static function normalizePath(string $path): string
    {
        $trimmed = rtrim($path, '/');
        if ($trimmed === '') {
            return $path === '' ? '/ui' : $path;
        }
        return $trimmed;
    }

    /**
     * @return array<string, string>|null
     */
    private static function matchTemplate(string $template, string $candidate): ?array
    {
        $templateParts = explode('/', $template);
        $candidateParts = explode('/', $candidate);
        if (count($templateParts) !== count($candidateParts)) {
            return null;
        }
        /** @var array<string, string> $params */
        $params = [];
        foreach ($templateParts as $i => $segment) {
            $actual = $candidateParts[$i];
            if ($segment !== '' && $segment[0] === '{' && substr($segment, -1) === '}') {
                $name = substr($segment, 1, -1);
                if ($actual === '') {
                    return null;
                }
                $params[$name] = rawurldecode($actual);
                continue;
            }
            if ($segment !== $actual) {
                return null;
            }
        }
        return $params;
    }

    /**
     * @return array{0: class-string, 1: string}
     */
    private static function splitHandler(string $handler): array
    {
        $at = strpos($handler, '@');
        if ($at === false) {
            /** @var class-string $handler */
            return [$handler, '__invoke'];
        }
        /** @var class-string $class */
        $class = substr($handler, 0, $at);
        $method = substr($handler, $at + 1);
        return [$class, $method];
    }

    private function authSession(): ?AuthenticateSession
    {
        if ($this->container === null || !$this->container->has(AuthenticateSession::class)) {
            return null;
        }
        $svc = $this->container->get(AuthenticateSession::class);
        return $svc instanceof AuthenticateSession ? $svc : null;
    }

    private function notFound(): HtmlResponse
    {
        // Use session-aware session start so flash messages survive 404s.
        if ($this->container !== null && $this->container->has(SessionStore::class)) {
            $svc = $this->container->get(SessionStore::class);
            if ($svc instanceof SessionStore) {
                $svc->start();
            }
        }
        return HtmlResponse::of(
            404,
            '<!doctype html><meta charset="utf-8"><title>404 — Rucaro</title>'
            . '<h1>404</h1><p>ページが見つかりません。</p>'
            . '<p><a href="/ui/dashboard">ダッシュボードへ戻る</a></p>',
        );
    }
}
