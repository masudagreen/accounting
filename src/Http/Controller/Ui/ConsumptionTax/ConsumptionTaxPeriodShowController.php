<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/consumption-tax/periods/{id} — show a period with action buttons
 * (calculate / report).
 */
final readonly class ConsumptionTaxPeriodShowController
{
    public const CSRF_FORM_ID = 'ui_consumption_tax_period_show';

    public function __construct(
        private ConsumptionTaxPeriodRepositoryInterface $periods,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
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
        $period = $this->periods->findById($id);
        if ($period === null || $period->entityId !== $entityId) {
            return HtmlResponse::notFound('申告期間が見つかりません。');
        }

        $data = [
            'page_title'           => '消費税申告期間詳細',
            'active_nav'           => 'consumption_tax',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'csrf_form_token'      => $this->csrf->generateToken(self::CSRF_FORM_ID),
            'csrf_form_field'      => self::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => [],
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'period'               => self::periodToArray($period),
        ];
        return HtmlResponse::ok($this->view->render('consumption_tax/period_show.html.tpl', $data));
    }

    /**
     * @return array<string, mixed>
     */
    private static function periodToArray(ConsumptionTaxPeriod $p): array
    {
        return [
            'id'              => $p->id,
            'fiscalTermId'    => $p->fiscalTermId,
            'periodFrom'      => $p->periodFrom->format('Y-m-d'),
            'periodTo'        => $p->periodTo->format('Y-m-d'),
            'method'          => $p->calculationMethod->value,
            'methodLabel'     => $p->calculationMethod->label(),
            'simplifiedLabel' => $p->simplifiedBusinessCategory?->label() ?? '',
            'isInterim'       => $p->isInterim,
            'status'          => $p->settlementStatus,
            'settledAt'       => $p->settledAt?->format('Y-m-d H:i') ?? '',
        ];
    }
}
