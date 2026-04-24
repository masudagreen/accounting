<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListInvoiceRegistrationsUseCase;
use Rucaro\Application\ConsumptionTax\UpsertInvoiceRegistrationUseCase;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/consumption-tax/invoice-registrations — list + new-form on one page
 * POST /ui/consumption-tax/invoice-registrations — upsert a row (id optional)
 */
final readonly class InvoiceRegistrationController
{
    public const CSRF_FORM_ID = 'ui_invoice_registration';

    public function __construct(
        private ListInvoiceRegistrationsUseCase $listRegs,
        private UpsertInvoiceRegistrationUseCase $upsert,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request): HtmlResponse
    {
        unset($request);
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        return $this->render($entityId, [], 200);
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/consumption-tax/invoice-registrations');
        }
        $id        = PlanningFormSupport::nullableStr($body, 'id');
        $name      = PlanningFormSupport::str($body, 'counterparty_name');
        $regNumber = PlanningFormSupport::nullableStr($body, 'registration_number');
        $isReg     = PlanningFormSupport::bool($body['is_registered'] ?? null);
        $from      = PlanningFormSupport::nullableStr($body, 'registered_from');
        $until     = PlanningFormSupport::nullableStr($body, 'registered_until');
        $notes     = PlanningFormSupport::nullableStr($body, 'notes');

        $errors = [];
        if ($name === '') {
            $errors['counterparty_name'] = ['相手先名を入力してください。'];
        }

        if ($errors === []) {
            try {
                $this->upsert->execute(
                    id: $id,
                    entityId: $entityId,
                    counterpartyName: $name,
                    registrationNumber: $regNumber,
                    isRegistered: $isReg,
                    registeredFrom: $from,
                    registeredUntil: $until,
                    notes: $notes,
                );
                $this->flash->addSuccess('インボイス登録情報を保存しました。');
                return HtmlResponse::redirect('/ui/consumption-tax/invoice-registrations');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['保存に失敗しました: ' . $e->getMessage()];
            }
        }
        return $this->render($entityId, $errors, 422);
    }

    /**
     * @param array<string, list<string>> $errors
     */
    private function render(string $entityId, array $errors, int $status): HtmlResponse
    {
        $regs = $this->listRegs->execute($entityId);
        $items = array_map(
            static fn (InvoiceRegistration $r): array => [
                'id'                 => $r->id,
                'counterpartyName'   => $r->counterpartyName,
                'registrationNumber' => $r->registrationNumber ?? '',
                'isRegistered'       => $r->isRegistered,
                'registeredFrom'     => $r->registeredFrom?->format('Y-m-d') ?? '',
                'registeredUntil'    => $r->registeredUntil?->format('Y-m-d') ?? '',
                'notes'              => $r->notes ?? '',
            ],
            $regs,
        );
        $data = [
            'page_title'           => 'インボイス登録事業者',
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
            'items'                => $items,
            'form_errors'          => $errors,
        ];
        return HtmlResponse::of($status, $this->view->render('consumption_tax/invoice_registrations.html.tpl', $data));
    }
}
