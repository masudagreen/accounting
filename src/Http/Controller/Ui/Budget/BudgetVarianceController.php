<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Budget;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceInput;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase;
use Rucaro\Application\Budget\GetBudgetUseCase;
use Rucaro\Domain\Budget\BudgetVarianceRow;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/budgets/{id}/variance — render the budget-vs-actual table.
 */
final readonly class BudgetVarianceController
{
    public function __construct(
        private GetBudgetUseCase $getBudget,
        private AnalyzeBudgetVarianceUseCase $analyze,
        private PlanningUiContext $ctx,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function __invoke(ServerRequest $request, string $id = ''): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $budget = $this->getBudget->execute($id);
        if ($budget === null || $budget->entityId !== $entityId) {
            return HtmlResponse::notFound('予算が見つかりません。');
        }

        $term = $this->ctx->findFiscalTerm($budget->fiscalTermId);
        if ($term === null || $term['startDate'] === '' || $term['endDate'] === '') {
            $this->flash->addError('対象の会計期間情報が取得できません。');
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }
        try {
            $start = new DateTimeImmutable($term['startDate'], new DateTimeZone('UTC'));
            $termEnd = new DateTimeImmutable($term['endDate'], new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            $this->flash->addError('会計期間の日付解析に失敗しました: ' . $e->getMessage());
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }

        $asOfRaw = $request->queryString('asOf');
        try {
            $asOf = $asOfRaw !== null
                ? new DateTimeImmutable($asOfRaw, new DateTimeZone('UTC'))
                : $this->clock->getCurrentTime();
        } catch (\Exception) {
            $asOf = $this->clock->getCurrentTime();
        }
        if ($asOf > $termEnd) {
            $asOf = $termEnd;
        }

        try {
            $analysis = $this->analyze->execute(new AnalyzeBudgetVarianceInput(
                budgetId: $budget->id,
                fiscalTermStartDate: $start,
                asOf: $asOf,
                currencyCode: 'JPY',
            ));
        } catch (\Throwable $e) {
            $this->flash->addError('予実対比の計算に失敗しました: ' . $e->getMessage());
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }

        $rows = array_map(
            static fn (BudgetVarianceRow $r): array => [
                'accountTitleCode' => $r->accountTitleCode,
                'accountTitleName' => $r->accountTitleName,
                'budgetAmount'     => $r->budgetAmount,
                'actualAmount'     => $r->actualAmount,
                'varianceAmount'   => $r->varianceAmount,
                'usageRate'        => $r->usageRatePercent ?? '',
            ],
            $analysis->rows,
        );

        $data = [
            'page_title'           => '予実対比',
            'active_nav'           => 'budgets',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => [],
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'budget'               => [
                'id'     => $budget->id,
                'name'   => $budget->name,
                'status' => $budget->status->value,
            ],
            'period_from'          => $start->format('Y-m-d'),
            'as_of'                => $asOf->format('Y-m-d'),
            'rows'                 => $rows,
        ];
        return HtmlResponse::ok($this->view->render('budgets/variance.html.tpl', $data));
    }
}
