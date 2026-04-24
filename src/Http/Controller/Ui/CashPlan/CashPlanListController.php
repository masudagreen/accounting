<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\CashPlan;

use Rucaro\Application\CashPlan\ListCashPlansUseCase;
use Rucaro\Domain\CashPlan\CashPlan;
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
 * GET /ui/cash-plans — list 資金繰り計画 for the current entity.
 */
final readonly class CashPlanListController
{
    public function __construct(
        private ListCashPlansUseCase $listPlans,
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
        try {
            $plans = $this->listPlans->execute($entityId, $fiscalTermId);
        } catch (\Throwable $e) {
            $this->flash->addError('資金繰り計画の取得に失敗しました: ' . $e->getMessage());
            $plans = [];
        }

        $items = array_map(
            static fn (CashPlan $p): array => [
                'id'             => $p->id,
                'name'           => $p->name,
                'openingBalance' => $p->openingBalance,
                'closingBalance' => $p->closingBalance(12),
                'currency'       => $p->currencyCode,
                'entryCount'     => count($p->entries),
                'updatedAt'      => $p->updatedAt->format('Y-m-d H:i'),
            ],
            $plans,
        );

        $data = [
            'page_title'           => '資金繰り計画',
            'active_nav'           => 'cash_plans',
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
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
        ];
        return HtmlResponse::ok($this->view->render('cash_plans/list.html.tpl', $data));
    }
}
