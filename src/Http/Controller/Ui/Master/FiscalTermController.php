<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Master;

use Rucaro\Application\FiscalTerm\CreateFiscalTermUseCase;
use Rucaro\Application\FiscalTerm\CreateFiscalTermUseCaseInput;
use Rucaro\Application\FiscalTerm\DeleteFiscalTermUseCase;
use Rucaro\Application\FiscalTerm\UpdateFiscalTermUseCase;
use Rucaro\Application\FiscalTerm\UpdateFiscalTermUseCaseInput;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FiscalTerm\FiscalTerm;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * CRUD controller for 会計期 (fiscal_terms) master.
 */
final readonly class FiscalTermController
{
    public const CSRF_FORM_ID = 'ui_master_fiscal_term';

    public function __construct(
        private FiscalTermRepositoryInterface $repo,
        private CreateFiscalTermUseCase $createUseCase,
        private UpdateFiscalTermUseCase $updateUseCase,
        private DeleteFiscalTermUseCase $deleteUseCase,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function list(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $terms = $this->repo->listByEntity($entityId);
        $rows = array_map(
            static fn (FiscalTerm $t): array => [
                'id'           => $t->id,
                'fiscalPeriod' => $t->fiscalPeriod,
                'startDate'    => $t->startDate->format('Y-m-d'),
                'endDate'      => $t->endDate->format('Y-m-d'),
                'isClosed'     => $t->isClosed,
            ],
            $terms,
        );
        return HtmlResponse::ok($this->view->render('masters/fiscal-terms/list.html.tpl', array_merge(
            $this->commonViewData('会計期マスタ'),
            ['rows' => $rows, 'total' => count($rows)],
        )));
    }

    public function newShow(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        return $this->renderForm(
            mode: 'new',
            formAction: '/ui/masters/fiscal-terms/new',
            values: self::blankValues(),
            errors: [],
            status: 200,
        );
    }

    public function newSubmit(ServerRequest $request): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $body = MasterFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, MasterFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms/new');
        }
        $values = self::valuesFromBody($body);
        try {
            $this->createUseCase->execute(new CreateFiscalTermUseCaseInput(
                entityId: $entityId,
                fiscalPeriod: (int) $values['fiscal_period'],
                startDate: $values['start_date'],
                endDate: $values['end_date'],
                isClosed: $values['is_closed'] === '1',
            ));
            $this->flash->addSuccess('会計期を登録しました。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/fiscal-terms/new',
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/fiscal-terms/new',
                values: $values,
                errors: ['_' => ['登録に失敗しました: ' . $e->getMessage()]],
                status: 500,
            );
        }
    }

    public function editShow(ServerRequest $request, string $id): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        $existing = $this->repo->findById($id);
        if ($existing === null) {
            $this->flash->addError('対象の会計期が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        }
        return $this->renderForm(
            mode: 'edit',
            formAction: '/ui/masters/fiscal-terms/' . $existing->id,
            values: self::valuesFromEntity($existing),
            errors: [],
            status: 200,
        );
    }

    public function editSubmit(ServerRequest $request, string $id): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        $body = MasterFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, MasterFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms/' . $id);
        }
        $values = self::valuesFromBody($body);
        try {
            $this->updateUseCase->execute(new UpdateFiscalTermUseCaseInput(
                id: $id,
                fiscalPeriod: (int) $values['fiscal_period'],
                startDate: $values['start_date'],
                endDate: $values['end_date'],
                isClosed: $values['is_closed'] === '1',
            ));
            $this->flash->addSuccess('会計期を更新しました。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の会計期が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/fiscal-terms/' . $id,
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/fiscal-terms/' . $id,
                values: $values,
                errors: ['_' => ['更新に失敗しました: ' . $e->getMessage()]],
                status: 500,
            );
        }
    }

    public function deleteShow(ServerRequest $request, string $id): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        $existing = $this->repo->findById($id);
        if ($existing === null) {
            $this->flash->addError('対象の会計期が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        }
        $data = array_merge(
            $this->commonViewData('会計期の削除確認'),
            [
                'target' => [
                    'id'           => $existing->id,
                    'fiscalPeriod' => $existing->fiscalPeriod,
                    'startDate'    => $existing->startDate->format('Y-m-d'),
                    'endDate'      => $existing->endDate->format('Y-m-d'),
                    'isClosed'     => $existing->isClosed,
                ],
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID . '_delete'),
            ],
        );
        return HtmlResponse::ok($this->view->render('masters/fiscal-terms/delete-confirm.html.tpl', $data));
    }

    public function delete(ServerRequest $request, string $id): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        $body = MasterFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID . '_delete', MasterFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/masters/fiscal-terms');
        }
        try {
            $this->deleteUseCase->execute($id);
            $this->flash->addSuccess('会計期を削除しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の会計期が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/masters/fiscal-terms');
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
     * @return array<string, mixed>
     */
    private function commonViewData(string $title): array
    {
        return [
            'page_title'         => $title,
            'active_nav'         => 'masters',
            'active_master'      => 'fiscal_terms',
            'csrf_logout_token'  => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'  => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'  => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'  => EntitySwitchController::CSRF_FORM_ID,
            'csrf_delete_token'  => $this->csrf->generateToken(self::CSRF_FORM_ID . '_delete'),
            'display_name'       => $this->session->getDisplayName() ?? '',
            'user_email'         => $this->session->getEmail() ?? '',
            'entities'           => [],
            'selected_entity_id' => $this->session->getSelectedEntity() ?? '',
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'     => $this->flash->consume(),
        ];
    }

    /**
     * @param array<string, string> $values
     * @param array<string, list<string>> $errors
     */
    private function renderForm(
        string $mode,
        string $formAction,
        array $values,
        array $errors,
        int $status,
    ): HtmlResponse {
        $data = array_merge(
            $this->commonViewData($mode === 'new' ? '会計期の新規追加' : '会計期の編集'),
            [
                'form_mode'       => $mode,
                'form_action'     => $formAction,
                'form_values'     => $values,
                'form_errors'     => $errors,
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID),
                'csrf_form_field' => self::CSRF_FORM_ID,
            ],
        );
        return HtmlResponse::of($status, $this->view->render('masters/fiscal-terms/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankValues(): array
    {
        return [
            'fiscal_period' => '1',
            'start_date'    => '',
            'end_date'      => '',
            'is_closed'     => '0',
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, string>
     */
    private static function valuesFromBody(array $body): array
    {
        return [
            'fiscal_period' => (string) MasterFormSupport::int($body, 'fiscal_period', 1),
            'start_date'    => MasterFormSupport::str($body, 'start_date'),
            'end_date'      => MasterFormSupport::str($body, 'end_date'),
            'is_closed'     => MasterFormSupport::bool($body, 'is_closed') ? '1' : '0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function valuesFromEntity(FiscalTerm $t): array
    {
        return [
            'fiscal_period' => (string) $t->fiscalPeriod,
            'start_date'    => $t->startDate->format('Y-m-d'),
            'end_date'      => $t->endDate->format('Y-m-d'),
            'is_closed'     => $t->isClosed ? '1' : '0',
        ];
    }
}
