<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\ListSsAdjustmentsUseCase;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/ss-adjustments — list 株主資本等変動計算書 manual adjustments.
 */
final readonly class SsAdjustmentListController
{
    public function __construct(
        private ListSsAdjustmentsUseCase $listAdjustments,
        private PlanningUiContext $ctx,
        private ClockInterface $clock,
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

        $terms = $this->ctx->fiscalTermsForEntity($entityId);
        $fiscalTermId = $request->queryString('fiscalTermId')
            ?? $this->session->getSelectedFiscalTerm()
            ?? PlanningUiContext::defaultFiscalTermId($terms, $this->clock->getCurrentTime());

        $items = [];
        if ($fiscalTermId !== null && $fiscalTermId !== '') {
            try {
                $adjustments = $this->listAdjustments->execute($entityId, $fiscalTermId);
                $items = array_map(
                    static fn (SsManualAdjustment $a): array => [
                        'id'         => $a->id,
                        'section'    => $a->sectionCode->value,
                        'sectionLabel' => $a->sectionCode->label(),
                        'changeType' => $a->changeType->value,
                        'changeLabel'=> $a->changeType->label(),
                        'amount'     => $a->amount,
                        'label'      => $a->label,
                        'sortOrder'  => $a->sortOrder,
                        'notes'      => $a->notes ?? '',
                    ],
                    $adjustments,
                );
            } catch (\Throwable $e) {
                $this->flash->addError('純資産変動調整の取得に失敗しました: ' . $e->getMessage());
            }
        }

        $data = [
            'page_title'           => '純資産変動調整',
            'active_nav'           => 'ss_adjustments',
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
            'fiscal_terms'         => $terms,
            'filter_fiscal_term'   => $fiscalTermId ?? '',
        ];
        return HtmlResponse::ok($this->view->render('ss_adjustments/list.html.tpl', $data));
    }
}
