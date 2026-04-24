<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Budget;

use Rucaro\Application\Budget\ListBudgetsUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/budgets — list budgets for the current entity. Filters on status
 * and fiscal term via query string.
 */
final readonly class BudgetListController
{
    public function __construct(
        private ListBudgetsUseCase $listBudgets,
        private PlanningUiContext $ctx,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $fiscalTermId = $request->queryString('fiscalTermId');
        $statusRaw    = $request->queryString('status');
        $status = null;
        if ($statusRaw !== null) {
            $status = BudgetStatus::tryFrom($statusRaw);
        }

        try {
            $budgets = $this->listBudgets->execute($entityId, $fiscalTermId, $status);
        } catch (\Throwable $e) {
            $this->flash->addError('予算一覧の取得に失敗しました: ' . $e->getMessage());
            $budgets = [];
        }

        $items = array_map(
            static fn (Budget $b): array => [
                'id'           => $b->id,
                'name'         => $b->name,
                'status'       => $b->status->value,
                'fiscalTermId' => $b->fiscalTermId,
                'annualTotal'  => $b->annualTotal(),
                'lineCount'    => count($b->lineItems),
                'updatedAt'    => $b->updatedAt->format('Y-m-d H:i'),
            ],
            $budgets,
        );

        $data = [
            'page_title'           => '予算一覧',
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
            'items'                => $items,
            'total'                => count($items),
            'filter_fiscal_term'   => $fiscalTermId ?? '',
            'filter_status'        => $status?->value ?? '',
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
            'status_options'       => ['draft', 'approved', 'locked'],
        ];
        return HtmlResponse::ok($this->view->render('budgets/list.html.tpl', $data));
    }
}
