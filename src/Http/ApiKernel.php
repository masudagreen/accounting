<?php

declare(strict_types=1);

namespace Rucaro\Http;

use Psr\Container\ContainerInterface;
use Rucaro\Http\Controller\AccountTitle\ListAccountTitleController;
use Rucaro\Http\Controller\Approval\GetApprovalController;
use Rucaro\Http\Controller\Approval\PostApprovalController;
use Rucaro\Http\Controller\Approval\RequestApprovalController;
use Rucaro\Http\Controller\Approval\ResendApprovalController;
use Rucaro\Http\Controller\Auth\LoginController;
use Rucaro\Http\Controller\Auth\MeController;
use Rucaro\Http\Controller\BlueReturn\CreateBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\DeleteBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\FinalizeBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\GetBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\ListBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\UpdateBlueReturnController;
use Rucaro\Http\Controller\BreakEvenPoint\GetBreakEvenPointController;
use Rucaro\Http\Controller\BreakEvenPoint\ListCvpClassificationController;
use Rucaro\Http\Controller\BreakEvenPoint\PutCvpClassificationController;
use Rucaro\Http\Controller\Budget\ApproveBudgetController;
use Rucaro\Http\Controller\Budget\CreateBudgetController;
use Rucaro\Http\Controller\Budget\DeleteBudgetController;
use Rucaro\Http\Controller\Budget\GetBudgetController;
use Rucaro\Http\Controller\Budget\GetBudgetVarianceController;
use Rucaro\Http\Controller\Budget\ListBudgetController;
use Rucaro\Http\Controller\Budget\LockBudgetController;
use Rucaro\Http\Controller\Budget\UpdateBudgetController;
use Rucaro\Http\Controller\CashPlan\CreateCashPlanController;
use Rucaro\Http\Controller\CashPlan\DeleteCashPlanController;
use Rucaro\Http\Controller\CashPlan\GetCashPlanController;
use Rucaro\Http\Controller\CashPlan\ListCashPlanController;
use Rucaro\Http\Controller\CashPlan\UpdateCashPlanController;
use Rucaro\Http\Controller\ConsumptionTax\CalculateConsumptionTaxController;
use Rucaro\Http\Controller\ConsumptionTax\CreateConsumptionTaxPeriodController;
use Rucaro\Http\Controller\ConsumptionTax\GetConsumptionTaxReportController;
use Rucaro\Http\Controller\ConsumptionTax\ListAccountTitleTaxDefaultsController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxCategoriesController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxPeriodsController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxRatesController;
use Rucaro\Http\Controller\ConsumptionTax\ListInvoiceRegistrationsController;
use Rucaro\Http\Controller\ConsumptionTax\PutAccountTitleTaxDefaultsController;
use Rucaro\Http\Controller\ConsumptionTax\UpsertInvoiceRegistrationController;
use Rucaro\Http\Controller\Entity\ListEntityController;
use Rucaro\Http\Controller\FinancialStatement\GetFinancialStatementController;
use Rucaro\Http\Controller\FinancialStatement\Multi\GetMultiPeriodFinancialStatementController;
use Rucaro\Http\Controller\FinancialStatementNotes\BulkImportFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\CreateFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\DeleteFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\ExportFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\GetFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\ListFsNoteTemplatesController;
use Rucaro\Http\Controller\FinancialStatementNotes\ListFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\ReorderFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\UpdateFsNoteController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\CreateSsAdjustmentController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\DeleteSsAdjustmentController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\GetStatementOfChangesInEquityController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\ListSsAdjustmentsController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\UpdateSsAdjustmentController;
use Rucaro\Http\Controller\FixedAsset\CreateFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\DisposeFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\GenerateDepreciationController;
use Rucaro\Http\Controller\FixedAsset\GetFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\GetFixedAssetLedgerController;
use Rucaro\Http\Controller\FixedAsset\ListFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\PostDepreciationJournalController;
use Rucaro\Http\Controller\FixedAsset\UpdateFixedAssetController;
use Rucaro\Http\Controller\Journal\ApproveJournalController;
use Rucaro\Http\Controller\Journal\CreateJournalController;
use Rucaro\Http\Controller\Journal\DeleteJournalController;
use Rucaro\Http\Controller\Journal\GetJournalController;
use Rucaro\Http\Controller\Journal\ListJournalController;
use Rucaro\Http\Controller\Journal\UpdateJournalController;
use Rucaro\Http\Controller\Ledger\GetLedgerController;
use Rucaro\Http\Controller\TrialBalance\GetTrialBalanceController;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;

/**
 * FastRoute-backed kernel for `/api/v1/*`.
 *
 * Keeps {@see JsonResponse::of()} for the trivial healthz probe (unauthenticated)
 * and delegates everything else to controllers resolved from the PSR-11
 * container so the `public/api/v1/index.php` entry point stays thin.
 *
 * When FastRoute is unavailable (e.g. minimal Phase 1 install without
 * `composer install`) the kernel falls back to a hand-rolled matcher.
 */
final class ApiKernel
{
    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {
    }

    public function handle(?ServerRequest $request = null): JsonResponse
    {
        $request ??= ServerRequest::fromGlobals();

        // Healthz stays pure and unauthenticated even when no container is wired.
        if ($request->method === 'GET' && $request->path === '/api/v1/healthz') {
            return JsonResponse::of(200, [
                'success' => true,
                'data'    => ['status' => 'ok'],
                'error'   => null,
                'meta'    => null,
            ]);
        }

        if ($this->container === null) {
            return ErrorResponse::of(
                503,
                'CONTAINER_UNAVAILABLE',
                'DI container is not wired for this route.',
            );
        }

        if (class_exists(\FastRoute\RouteCollector::class)) {
            return $this->handleWithFastRoute($request, $this->container);
        }
        return $this->handleFallback($request, $this->container);
    }

    private function handleWithFastRoute(ServerRequest $request, ContainerInterface $container): JsonResponse
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r): void {
            $r->addRoute('POST', '/api/v1/auth/login', LoginController::class);
            $r->addRoute('GET', '/api/v1/auth/me', MeController::class);
            $r->addRoute('GET', '/api/v1/entities', ListEntityController::class);
            $r->addRoute('GET', '/api/v1/account-titles', ListAccountTitleController::class);
            $r->addRoute('GET', '/api/v1/journals', ListJournalController::class);
            $r->addRoute('POST', '/api/v1/journals', CreateJournalController::class);
            $r->addRoute('GET', '/api/v1/journals/get', GetJournalController::class);
            $r->addRoute('PATCH', '/api/v1/journals/update', UpdateJournalController::class);
            $r->addRoute('DELETE', '/api/v1/journals/delete', DeleteJournalController::class);
            $r->addRoute('POST', '/api/v1/journals/approve', ApproveJournalController::class);
            $r->addRoute('GET', '/api/v1/trial-balance', GetTrialBalanceController::class);
            $r->addRoute('GET', '/api/v1/financial-statements', GetFinancialStatementController::class);
            $r->addRoute('GET', '/api/v1/financial-statements/multi', GetMultiPeriodFinancialStatementController::class);
            $r->addRoute('GET', '/api/v1/ledger', GetLedgerController::class);
            // Phase 5.2 approval pipeline. Path parameters are merged into the
            // request query so controllers can read them via `queryString`.
            $r->addRoute('POST', '/api/v1/journals/{id}/request-approval', RequestApprovalController::class);
            $r->addRoute('GET', '/api/v1/approvals/{token}', GetApprovalController::class);
            $r->addRoute('POST', '/api/v1/approvals/{token}', PostApprovalController::class);
            $r->addRoute('POST', '/api/v1/approvals/{prefix}/resend', ResendApprovalController::class);
            // Phase 6 Wave 6-D: fixed assets port.
            $r->addRoute('GET', '/api/v1/fixed-assets', ListFixedAssetController::class);
            $r->addRoute('POST', '/api/v1/fixed-assets', CreateFixedAssetController::class);
            $r->addRoute('GET', '/api/v1/fixed-assets/ledger', GetFixedAssetLedgerController::class);
            $r->addRoute('POST', '/api/v1/fixed-assets/depreciate', GenerateDepreciationController::class);
            $r->addRoute('POST', '/api/v1/fixed-assets/depreciate-all', PostDepreciationJournalController::class);
            $r->addRoute('GET', '/api/v1/fixed-assets/{id}', GetFixedAssetController::class);
            $r->addRoute('PATCH', '/api/v1/fixed-assets/{id}', UpdateFixedAssetController::class);
            $r->addRoute('POST', '/api/v1/fixed-assets/{id}/dispose', DisposeFixedAssetController::class);
            $r->addRoute('POST', '/api/v1/fixed-assets/{id}/depreciate', GenerateDepreciationController::class);
            // Phase 6 Wave 6-E: cash plan port.
            $r->addRoute('GET', '/api/v1/cash-plans', ListCashPlanController::class);
            $r->addRoute('POST', '/api/v1/cash-plans', CreateCashPlanController::class);
            $r->addRoute('GET', '/api/v1/cash-plans/{id}', GetCashPlanController::class);
            $r->addRoute('PATCH', '/api/v1/cash-plans/{id}', UpdateCashPlanController::class);
            $r->addRoute('DELETE', '/api/v1/cash-plans/{id}', DeleteCashPlanController::class);
            // Phase 6 Wave 6-E: break-even point port.
            $r->addRoute('GET', '/api/v1/break-even-point', GetBreakEvenPointController::class);
            $r->addRoute('GET', '/api/v1/cvp-classifications', ListCvpClassificationController::class);
            $r->addRoute('PUT', '/api/v1/cvp-classifications', PutCvpClassificationController::class);
            // Phase 6 Wave 6-F: consumption-tax port.
            $r->addRoute('GET',  '/api/v1/consumption-tax/rates', ListConsumptionTaxRatesController::class);
            $r->addRoute('GET',  '/api/v1/consumption-tax/categories', ListConsumptionTaxCategoriesController::class);
            $r->addRoute('GET',  '/api/v1/consumption-tax/account-title-defaults', ListAccountTitleTaxDefaultsController::class);
            $r->addRoute('PUT',  '/api/v1/consumption-tax/account-title-defaults', PutAccountTitleTaxDefaultsController::class);
            $r->addRoute('GET',  '/api/v1/consumption-tax/invoice-registrations', ListInvoiceRegistrationsController::class);
            $r->addRoute('POST', '/api/v1/consumption-tax/invoice-registrations', UpsertInvoiceRegistrationController::class);
            $r->addRoute('PATCH', '/api/v1/consumption-tax/invoice-registrations/{id}', UpsertInvoiceRegistrationController::class);
            $r->addRoute('GET',  '/api/v1/consumption-tax/periods', ListConsumptionTaxPeriodsController::class);
            $r->addRoute('POST', '/api/v1/consumption-tax/periods', CreateConsumptionTaxPeriodController::class);
            $r->addRoute('POST', '/api/v1/consumption-tax/periods/{id}/calculate', CalculateConsumptionTaxController::class);
            $r->addRoute('GET',  '/api/v1/consumption-tax/periods/{id}/report', GetConsumptionTaxReportController::class);
            // Phase 6 Wave 6-G: budget port.
            $r->addRoute('GET',    '/api/v1/budgets', ListBudgetController::class);
            $r->addRoute('POST',   '/api/v1/budgets', CreateBudgetController::class);
            $r->addRoute('GET',    '/api/v1/budgets/{id}', GetBudgetController::class);
            $r->addRoute('PATCH',  '/api/v1/budgets/{id}', UpdateBudgetController::class);
            $r->addRoute('DELETE', '/api/v1/budgets/{id}', DeleteBudgetController::class);
            $r->addRoute('POST',   '/api/v1/budgets/{id}/approve', ApproveBudgetController::class);
            $r->addRoute('POST',   '/api/v1/budgets/{id}/lock', LockBudgetController::class);
            $r->addRoute('GET',    '/api/v1/budgets/{id}/variance-analysis', GetBudgetVarianceController::class);
            // Phase 6 Wave 6-H-2: statement of changes in equity port.
            $r->addRoute('GET',    '/api/v1/statement-of-changes-in-equity', GetStatementOfChangesInEquityController::class);
            $r->addRoute('GET',    '/api/v1/ss-adjustments', ListSsAdjustmentsController::class);
            $r->addRoute('POST',   '/api/v1/ss-adjustments', CreateSsAdjustmentController::class);
            $r->addRoute('PATCH',  '/api/v1/ss-adjustments/{id}', UpdateSsAdjustmentController::class);
            $r->addRoute('DELETE', '/api/v1/ss-adjustments/{id}', DeleteSsAdjustmentController::class);
            // Phase 6 Wave 6-H-1: blue return port (個人事業主 青色申告決算書).
            $r->addRoute('GET',    '/api/v1/blue-returns', ListBlueReturnController::class);
            $r->addRoute('POST',   '/api/v1/blue-returns', CreateBlueReturnController::class);
            $r->addRoute('GET',    '/api/v1/blue-returns/{id}', GetBlueReturnController::class);
            $r->addRoute('PATCH',  '/api/v1/blue-returns/{id}', UpdateBlueReturnController::class);
            $r->addRoute('DELETE', '/api/v1/blue-returns/{id}', DeleteBlueReturnController::class);
            $r->addRoute('POST',   '/api/v1/blue-returns/{id}/finalize', FinalizeBlueReturnController::class);
            // Phase 6 Wave 6-H-3: financial statement notes port (注記表).
            $r->addRoute('GET',    '/api/v1/fs-note-templates', ListFsNoteTemplatesController::class);
            $r->addRoute('GET',    '/api/v1/fs-notes', ListFsNotesController::class);
            $r->addRoute('POST',   '/api/v1/fs-notes', CreateFsNoteController::class);
            $r->addRoute('POST',   '/api/v1/fs-notes/bulk-import', BulkImportFsNotesController::class);
            $r->addRoute('POST',   '/api/v1/fs-notes/reorder', ReorderFsNotesController::class);
            $r->addRoute('GET',    '/api/v1/fs-notes/export', ExportFsNotesController::class);
            $r->addRoute('GET',    '/api/v1/fs-notes/{id}', GetFsNoteController::class);
            $r->addRoute('PATCH',  '/api/v1/fs-notes/{id}', UpdateFsNoteController::class);
            $r->addRoute('DELETE', '/api/v1/fs-notes/{id}', DeleteFsNoteController::class);
        });

        $info = $dispatcher->dispatch($request->method, $request->path);
        switch ($info[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                return ErrorResponse::notFound();
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                /** @var list<string> $allowed */
                $allowed = $info[1];
                return ErrorResponse::of(
                    405,
                    'METHOD_NOT_ALLOWED',
                    'Method Not Allowed',
                    null,
                    ['Allow' => implode(', ', $allowed)],
                );
            case \FastRoute\Dispatcher::FOUND:
                /** @var class-string $handler */
                $handler = $info[1];
                /** @var array<string, string> $params */
                $params = is_array($info[2] ?? null) ? $info[2] : [];
                $req = $params === [] ? $request : self::mergePathParams($request, $params);
                return $this->invoke($handler, $req, $container);
        }
        return ErrorResponse::notFound();
    }

    /**
     * @param array<string, string> $params
     */
    private static function mergePathParams(ServerRequest $request, array $params): ServerRequest
    {
        $query = $request->query;
        foreach ($params as $k => $v) {
            $query[$k] = $v;
        }
        return new ServerRequest(
            method: $request->method,
            path: $request->path,
            headers: $request->headers,
            query: $query,
            json: $request->json,
            rawBody: $request->rawBody,
        );
    }

    private function handleFallback(ServerRequest $request, ContainerInterface $container): JsonResponse
    {
        $routes = [
            ['POST', '/api/v1/auth/login', LoginController::class],
            ['GET', '/api/v1/auth/me', MeController::class],
            ['GET', '/api/v1/entities', ListEntityController::class],
            ['GET', '/api/v1/account-titles', ListAccountTitleController::class],
            ['GET', '/api/v1/journals', ListJournalController::class],
            ['POST', '/api/v1/journals', CreateJournalController::class],
            ['GET', '/api/v1/journals/get', GetJournalController::class],
            ['PATCH', '/api/v1/journals/update', UpdateJournalController::class],
            ['DELETE', '/api/v1/journals/delete', DeleteJournalController::class],
            ['POST', '/api/v1/journals/approve', ApproveJournalController::class],
            ['GET', '/api/v1/trial-balance', GetTrialBalanceController::class],
            ['GET', '/api/v1/financial-statements', GetFinancialStatementController::class],
            ['GET', '/api/v1/financial-statements/multi', GetMultiPeriodFinancialStatementController::class],
            ['GET', '/api/v1/ledger', GetLedgerController::class],
            ['POST', '/api/v1/approvals/request', RequestApprovalController::class],
            ['GET', '/api/v1/approvals/get', GetApprovalController::class],
            ['POST', '/api/v1/approvals/respond', PostApprovalController::class],
            ['POST', '/api/v1/approvals/resend', ResendApprovalController::class],
            ['GET', '/api/v1/fixed-assets', ListFixedAssetController::class],
            ['POST', '/api/v1/fixed-assets', CreateFixedAssetController::class],
            ['GET', '/api/v1/fixed-assets/ledger', GetFixedAssetLedgerController::class],
            ['POST', '/api/v1/fixed-assets/depreciate', GenerateDepreciationController::class],
            ['POST', '/api/v1/fixed-assets/depreciate-all', PostDepreciationJournalController::class],
            ['GET', '/api/v1/fixed-assets/get', GetFixedAssetController::class],
            ['PATCH', '/api/v1/fixed-assets/update', UpdateFixedAssetController::class],
            ['POST', '/api/v1/fixed-assets/dispose', DisposeFixedAssetController::class],
            ['GET', '/api/v1/cash-plans', ListCashPlanController::class],
            ['POST', '/api/v1/cash-plans', CreateCashPlanController::class],
            ['GET', '/api/v1/cash-plans/get', GetCashPlanController::class],
            ['PATCH', '/api/v1/cash-plans/update', UpdateCashPlanController::class],
            ['DELETE', '/api/v1/cash-plans/delete', DeleteCashPlanController::class],
            ['GET', '/api/v1/break-even-point', GetBreakEvenPointController::class],
            ['GET', '/api/v1/cvp-classifications', ListCvpClassificationController::class],
            ['PUT', '/api/v1/cvp-classifications', PutCvpClassificationController::class],
            ['GET', '/api/v1/consumption-tax/rates', ListConsumptionTaxRatesController::class],
            ['GET', '/api/v1/consumption-tax/categories', ListConsumptionTaxCategoriesController::class],
            ['GET', '/api/v1/consumption-tax/account-title-defaults', ListAccountTitleTaxDefaultsController::class],
            ['PUT', '/api/v1/consumption-tax/account-title-defaults', PutAccountTitleTaxDefaultsController::class],
            ['GET', '/api/v1/consumption-tax/invoice-registrations', ListInvoiceRegistrationsController::class],
            ['POST', '/api/v1/consumption-tax/invoice-registrations', UpsertInvoiceRegistrationController::class],
            ['GET', '/api/v1/consumption-tax/periods', ListConsumptionTaxPeriodsController::class],
            ['POST', '/api/v1/consumption-tax/periods', CreateConsumptionTaxPeriodController::class],
            ['POST', '/api/v1/consumption-tax/periods/calculate', CalculateConsumptionTaxController::class],
            ['GET', '/api/v1/consumption-tax/periods/report', GetConsumptionTaxReportController::class],
            ['GET',    '/api/v1/budgets', ListBudgetController::class],
            ['POST',   '/api/v1/budgets', CreateBudgetController::class],
            ['GET',    '/api/v1/budgets/get', GetBudgetController::class],
            ['PATCH',  '/api/v1/budgets/update', UpdateBudgetController::class],
            ['DELETE', '/api/v1/budgets/delete', DeleteBudgetController::class],
            ['POST',   '/api/v1/budgets/approve', ApproveBudgetController::class],
            ['POST',   '/api/v1/budgets/lock', LockBudgetController::class],
            ['GET',    '/api/v1/budgets/variance-analysis', GetBudgetVarianceController::class],
            ['GET',    '/api/v1/statement-of-changes-in-equity', GetStatementOfChangesInEquityController::class],
            ['GET',    '/api/v1/ss-adjustments', ListSsAdjustmentsController::class],
            ['POST',   '/api/v1/ss-adjustments', CreateSsAdjustmentController::class],
            ['PATCH',  '/api/v1/ss-adjustments/update', UpdateSsAdjustmentController::class],
            ['DELETE', '/api/v1/ss-adjustments/delete', DeleteSsAdjustmentController::class],
            ['GET',    '/api/v1/blue-returns', ListBlueReturnController::class],
            ['POST',   '/api/v1/blue-returns', CreateBlueReturnController::class],
            ['GET',    '/api/v1/blue-returns/get', GetBlueReturnController::class],
            ['PATCH',  '/api/v1/blue-returns/update', UpdateBlueReturnController::class],
            ['DELETE', '/api/v1/blue-returns/delete', DeleteBlueReturnController::class],
            ['POST',   '/api/v1/blue-returns/finalize', FinalizeBlueReturnController::class],
            ['GET',    '/api/v1/fs-note-templates', ListFsNoteTemplatesController::class],
            ['GET',    '/api/v1/fs-notes', ListFsNotesController::class],
            ['POST',   '/api/v1/fs-notes', CreateFsNoteController::class],
            ['GET',    '/api/v1/fs-notes/get', GetFsNoteController::class],
            ['PATCH',  '/api/v1/fs-notes/update', UpdateFsNoteController::class],
            ['DELETE', '/api/v1/fs-notes/delete', DeleteFsNoteController::class],
            ['POST',   '/api/v1/fs-notes/bulk-import', BulkImportFsNotesController::class],
            ['POST',   '/api/v1/fs-notes/reorder', ReorderFsNotesController::class],
            ['GET',    '/api/v1/fs-notes/export', ExportFsNotesController::class],
        ];
        foreach ($routes as [$method, $path, $handler]) {
            if ($request->method === $method && $request->path === $path) {
                return $this->invoke($handler, $request, $container);
            }
        }
        return ErrorResponse::notFound();
    }

    /**
     * @param class-string $handler
     */
    private function invoke(string $handler, ServerRequest $request, ContainerInterface $container): JsonResponse
    {
        /** @var object $controller */
        $controller = $container->get($handler);
        if (!is_callable($controller)) {
            return ErrorResponse::of(500, 'HANDLER_NOT_CALLABLE', 'Controller is not callable.');
        }
        /** @var JsonResponse $response */
        $response = $controller($request);
        return $response;
    }
}
