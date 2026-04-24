<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListConsumptionTaxPeriodsUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/consumption-tax/periods — list consumption-tax declaration periods.
 */
final readonly class ConsumptionTaxPeriodListController
{
    public function __construct(
        private ListConsumptionTaxPeriodsUseCase $listPeriods,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request): HtmlResponse
    {
        unset($request);
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        try {
            $periods = $this->listPeriods->execute($entityId);
        } catch (\Throwable $e) {
            $this->flash->addError('消費税申告期間の取得に失敗しました: ' . $e->getMessage());
            $periods = [];
        }

        $items = array_map(
            static fn (ConsumptionTaxPeriod $p): array => [
                'id'         => $p->id,
                'periodFrom' => $p->periodFrom->format('Y-m-d'),
                'periodTo'   => $p->periodTo->format('Y-m-d'),
                'method'     => $p->calculationMethod->value,
                'methodLabel'=> $p->calculationMethod->label(),
                'category'   => $p->simplifiedBusinessCategory?->label() ?? '',
                'isInterim'  => $p->isInterim,
                'status'     => $p->settlementStatus,
            ],
            $periods,
        );

        $data = [
            'page_title'           => '消費税申告期間',
            'active_nav'           => 'consumption_tax',
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
        ];
        return HtmlResponse::ok($this->view->render('consumption_tax/period_list.html.tpl', $data));
    }
}
