<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\CalculateConsumptionTaxUseCase;
use Rucaro\Application\ConsumptionTax\GenerateConsumptionTaxReportUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * POST /ui/consumption-tax/periods/{id}/calculate — run the calculator.
 * GET  /ui/consumption-tax/periods/{id}/report    — render the settlement.
 */
final readonly class ConsumptionTaxCalculateController
{
    public function __construct(
        private CalculateConsumptionTaxUseCase $calculate,
        private GenerateConsumptionTaxReportUseCase $report,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function calculateAction(ServerRequest $request, string $id = ''): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(ConsumptionTaxPeriodShowController::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/consumption-tax/periods/' . $id);
        }
        try {
            $this->calculate->execute($id);
            $this->flash->addSuccess('消費税の計算が完了しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の申告期間が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('計算に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/consumption-tax/periods/' . $id . '/report');
    }

    public function reportAction(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        try {
            $settlement = $this->report->execute($id);
        } catch (EntityNotFoundException) {
            return HtmlResponse::notFound('対象の申告期間が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('申告書生成に失敗しました: ' . $e->getMessage());
            return HtmlResponse::redirect('/ui/consumption-tax/periods/' . $id);
        }
        $data = [
            'page_title'           => '消費税申告書',
            'active_nav'           => 'consumption_tax',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => [],
            'selected_entity_id'   => (string) $this->session->getSelectedEntity(),
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'settlement'           => self::settlementToArray($settlement),
        ];
        return HtmlResponse::ok($this->view->render('consumption_tax/report.html.tpl', $data));
    }

    /**
     * @return array<string, mixed>
     */
    private static function settlementToArray(ConsumptionTaxSettlement $s): array
    {
        $split = $s->taxSplitNationalLocal();
        return [
            'periodId'                   => $s->period->id,
            'periodFrom'                 => $s->period->periodFrom->format('Y-m-d'),
            'periodTo'                   => $s->period->periodTo->format('Y-m-d'),
            'methodLabel'                => $s->method->label(),
            'taxableSales'               => $s->taxableSales,
            'nonTaxableSales'            => $s->nonTaxableSales,
            'exemptSales'                => $s->exemptSales,
            'untaxedSales'               => $s->untaxedSales,
            'totalSales'                 => $s->totalSales,
            'taxableSalesRatio'          => $s->taxableSalesRatio,
            'outputTax'                  => $s->outputTax,
            'deductibleInputTax'         => $s->deductibleInputTax,
            'adjustmentForNonRegistered' => $s->adjustmentForNonRegistered,
            'netTaxPayable'              => $s->netTaxPayable,
            'national'                   => $split['national'],
            'local'                      => $split['local'],
            'salesByRate'                => $s->salesByRate,
            'outputTaxByRate'            => $s->outputTaxByRate,
            'purchasesByRate'            => $s->purchasesByRate,
            'inputTaxByRate'             => $s->inputTaxByRate,
        ];
    }
}
