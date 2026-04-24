<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/ss-adjustments/new — blank adjustment form.
 * POST /ui/ss-adjustments/new — create an adjustment row.
 */
final readonly class SsAdjustmentNewController
{
    public const CSRF_FORM_ID = 'ui_ss_adjustment_new';

    public function __construct(
        private CreateSsAdjustmentUseCase $createAdjustment,
        private PlanningUiContext $ctx,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $terms = $this->ctx->fiscalTermsForEntity($entityId);
        $default = PlanningUiContext::defaultFiscalTermId($terms, $this->clock->getCurrentTime());
        return $this->renderForm(
            entityId: $entityId,
            form: self::blankForm($default ?? ($this->session->getSelectedFiscalTerm() ?? '')),
            errors: [],
            status: 200,
        );
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();

        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/ss-adjustments/new');
        }

        $form = [
            'fiscalTermId' => PlanningFormSupport::str($body, 'fiscal_term_id'),
            'sectionCode'  => PlanningFormSupport::str($body, 'section_code'),
            'changeType'   => PlanningFormSupport::str($body, 'change_type'),
            'amount'       => PlanningFormSupport::str($body, 'amount', '0'),
            'label'        => PlanningFormSupport::str($body, 'label'),
            'sortOrder'    => PlanningFormSupport::str($body, 'sort_order', '0'),
            'notes'        => PlanningFormSupport::str($body, 'notes'),
        ];

        $errors = [];
        if ($form['fiscalTermId'] === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        if ($form['label'] === '') {
            $errors['label'] = ['項目名を入力してください。'];
        }
        $section = SsSectionCode::tryFrom($form['sectionCode']);
        if ($section === null) {
            $errors['section_code'] = ['列 (section_code) を選択してください。'];
        }
        $change = SsChangeType::tryFrom($form['changeType']);
        if ($change === null) {
            $errors['change_type'] = ['変動事由を選択してください。'];
        }

        if ($errors === [] && $section !== null && $change !== null) {
            try {
                $this->createAdjustment->execute(new CreateSsAdjustmentInput(
                    entityId: $entityId,
                    fiscalTermId: $form['fiscalTermId'],
                    sectionCode: $section,
                    changeType: $change,
                    amount: PlanningFormSupport::normalizeAmount($form['amount']),
                    label: $form['label'],
                    sortOrder: (int) $form['sortOrder'],
                    notes: $form['notes'] === '' ? null : $form['notes'],
                ));
                $this->flash->addSuccess('純資産変動調整を登録しました。');
                return HtmlResponse::redirect('/ui/ss-adjustments?fiscalTermId=' . urlencode($form['fiscalTermId']));
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['登録に失敗しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm(entityId: $entityId, form: $form, errors: $errors, status: 422);
    }

    private function guard(): ?HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }
        return null;
    }

    /**
     * @param array<string, string> $form
     * @param array<string, list<string>> $errors
     */
    private function renderForm(string $entityId, array $form, array $errors, int $status): HtmlResponse
    {
        $data = [
            'page_title'           => '新規純資産変動調整',
            'active_nav'           => 'ss_adjustments',
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
            'form_mode'            => 'new',
            'form_action'          => '/ui/ss-adjustments/new',
            'form'                 => $form,
            'form_errors'          => $errors,
            'fiscal_terms'         => $this->ctx->fiscalTermsForEntity($entityId),
            'section_options'      => self::sectionOptions(),
            'change_options'       => self::changeOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('ss_adjustments/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankForm(string $defaultTermId): array
    {
        return [
            'fiscalTermId' => $defaultTermId,
            'sectionCode'  => SsSectionCode::CapitalStock->value,
            'changeType'   => SsChangeType::Other->value,
            'amount'       => '0',
            'label'        => '',
            'sortOrder'    => '0',
            'notes'        => '',
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function sectionOptions(): array
    {
        return array_map(
            static fn (SsSectionCode $s): array => [
                'value' => $s->value,
                'label' => $s->label(),
            ],
            SsSectionCode::ordered(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function changeOptions(): array
    {
        return array_map(
            static fn (SsChangeType $c): array => [
                'value' => $c->value,
                'label' => $c->label(),
            ],
            SsChangeType::cases(),
        );
    }
}
