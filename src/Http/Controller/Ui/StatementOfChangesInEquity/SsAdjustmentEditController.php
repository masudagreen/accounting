<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
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
 * GET  /ui/ss-adjustments/{id} — edit form prefilled from persisted row.
 * POST /ui/ss-adjustments/{id} — apply the update.
 */
final readonly class SsAdjustmentEditController
{
    public const CSRF_FORM_ID = 'ui_ss_adjustment_edit';

    public function __construct(
        private UpdateSsAdjustmentUseCase $update,
        private SsManualAdjustmentRepositoryInterface $repo,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
        $entityId = $this->resolveEntity();
        if ($entityId instanceof HtmlResponse) {
            return $entityId;
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $adj = $this->repo->findById($id);
        if ($adj === null || $adj->entityId !== $entityId) {
            return HtmlResponse::notFound('純資産変動調整が見つかりません。');
        }
        $form = [
            'id'           => $adj->id,
            'fiscalTermId' => $adj->fiscalTermId,
            'sectionCode'  => $adj->sectionCode->value,
            'changeType'   => $adj->changeType->value,
            'amount'       => $adj->amount,
            'label'        => $adj->label,
            'sortOrder'    => (string) $adj->sortOrder,
            'notes'        => $adj->notes ?? '',
        ];
        return $this->renderForm($form, [], 200);
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $entityId = $this->resolveEntity();
        if ($entityId instanceof HtmlResponse) {
            return $entityId;
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/ss-adjustments/' . $id);
        }

        $section = SsSectionCode::tryFrom(PlanningFormSupport::str($body, 'section_code'));
        $change  = SsChangeType::tryFrom(PlanningFormSupport::str($body, 'change_type'));
        $amount  = PlanningFormSupport::normalizeAmount(PlanningFormSupport::str($body, 'amount', '0'));
        $label   = PlanningFormSupport::str($body, 'label');
        $sort    = (int) PlanningFormSupport::str($body, 'sort_order', '0');
        $notes   = PlanningFormSupport::nullableStr($body, 'notes');

        try {
            $this->update->execute(new UpdateSsAdjustmentInput(
                id: $id,
                sectionCode: $section,
                changeType: $change,
                amount: $amount,
                label: $label === '' ? null : $label,
                sortOrder: $sort,
                notes: $notes,
            ));
            $this->flash->addSuccess('純資産変動調整を更新しました。');
        } catch (ValidationException $e) {
            $this->flash->addError('更新に失敗しました: ' . self::firstError($e));
        } catch (\Throwable $e) {
            $this->flash->addError('更新処理でエラーが発生しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/ss-adjustments/' . $id);
    }

    private function resolveEntity(): string|HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $eid = $this->session->getSelectedEntity();
        if ($eid === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        return $eid;
    }

    /**
     * @param array<string, string> $form
     * @param array<string, list<string>> $errors
     */
    private function renderForm(array $form, array $errors, int $status): HtmlResponse
    {
        $data = [
            'page_title'           => '純資産変動調整編集',
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
            'selected_entity_id'   => (string) $this->session->getSelectedEntity(),
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'form_mode'            => 'edit',
            'form_action'          => '/ui/ss-adjustments/' . $form['id'],
            'form'                 => $form,
            'form_errors'          => $errors,
            'fiscal_terms'         => [],
            'section_options'      => SsAdjustmentNewController::sectionOptions(),
            'change_options'       => SsAdjustmentNewController::changeOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('ss_adjustments/form.html.tpl', $data));
    }

    private static function firstError(ValidationException $e): string
    {
        foreach ($e->errors() as $msgs) {
            if (is_array($msgs) && isset($msgs[0]) && is_string($msgs[0])) {
                return $msgs[0];
            }
        }
        return $e->getMessage();
    }
}
