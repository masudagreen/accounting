<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Master;

use Rucaro\Application\SubAccountTitle\CreateSubAccountTitleUseCase;
use Rucaro\Application\SubAccountTitle\CreateSubAccountTitleUseCaseInput;
use Rucaro\Application\SubAccountTitle\DeleteSubAccountTitleUseCase;
use Rucaro\Application\SubAccountTitle\UpdateSubAccountTitleUseCase;
use Rucaro\Application\SubAccountTitle\UpdateSubAccountTitleUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\SubAccountTitle\SubAccountTitle;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * CRUD controller for the 補助科目 master. Depends on a parent
 * {@see AccountTitle}; the list page renders rows grouped by parent.
 */
final readonly class SubAccountTitleController
{
    public const CSRF_FORM_ID = 'ui_master_sub_account_title';

    public function __construct(
        private SubAccountTitleRepositoryInterface $repo,
        private AccountTitleRepositoryInterface $accountTitleRepo,
        private CreateSubAccountTitleUseCase $createUseCase,
        private UpdateSubAccountTitleUseCase $updateUseCase,
        private DeleteSubAccountTitleUseCase $deleteUseCase,
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

        $subs = $this->repo->listByEntity($entityId);
        $titles = $this->accountTitleRepo->findAllByEntity($entityId);
        /** @var array<string, array{id: string, code: string, name: string}> $titleMap */
        $titleMap = [];
        foreach ($titles as $t) {
            $titleMap[$t->id] = ['id' => $t->id, 'code' => $t->code, 'name' => $t->name];
        }
        $rows = array_map(
            static fn (SubAccountTitle $s): array => [
                'id'             => $s->id,
                'accountTitleId' => $s->accountTitleId,
                'code'           => $s->code,
                'name'           => $s->name,
                'sortOrder'      => $s->sortOrder,
                'isActive'       => $s->isActive,
            ],
            $subs,
        );
        return HtmlResponse::ok($this->view->render('masters/sub-account-titles/list.html.tpl', array_merge(
            $this->commonViewData('補助科目マスタ'),
            [
                'rows'      => $rows,
                'title_map' => $titleMap,
                'total'     => count($rows),
            ],
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
            formAction: '/ui/masters/sub-account-titles/new',
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
        $body = MasterFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, MasterFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles/new');
        }
        $values = self::valuesFromBody($body);
        try {
            $this->createUseCase->execute(new CreateSubAccountTitleUseCaseInput(
                accountTitleId: $values['account_title_id'],
                code: $values['code'],
                name: $values['name'],
                sortOrder: (int) $values['sort_order'],
                isActive: $values['is_active'] === '1',
            ));
            $this->flash->addSuccess('補助科目を登録しました。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/sub-account-titles/new',
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/sub-account-titles/new',
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
            $this->flash->addError('対象の補助科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        }
        return $this->renderForm(
            mode: 'edit',
            formAction: '/ui/masters/sub-account-titles/' . $existing->id,
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
            return HtmlResponse::redirect('/ui/masters/sub-account-titles/' . $id);
        }
        $values = self::valuesFromBody($body);
        try {
            $this->updateUseCase->execute(new UpdateSubAccountTitleUseCaseInput(
                id: $id,
                code: $values['code'],
                name: $values['name'],
                sortOrder: (int) $values['sort_order'],
                isActive: $values['is_active'] === '1',
            ));
            $this->flash->addSuccess('補助科目を更新しました。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の補助科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/sub-account-titles/' . $id,
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/sub-account-titles/' . $id,
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
            $this->flash->addError('対象の補助科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        }
        $parent = $this->accountTitleRepo->findById($existing->accountTitleId);
        $data = array_merge(
            $this->commonViewData('補助科目の削除確認'),
            [
                'target' => [
                    'id'           => $existing->id,
                    'code'         => $existing->code,
                    'name'         => $existing->name,
                    'parentCode'   => $parent?->code ?? '',
                    'parentName'   => $parent?->name ?? '',
                ],
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID . '_delete'),
            ],
        );
        return HtmlResponse::ok($this->view->render('masters/sub-account-titles/delete-confirm.html.tpl', $data));
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
            return HtmlResponse::redirect('/ui/masters/sub-account-titles');
        }
        try {
            $this->deleteUseCase->execute($id);
            $this->flash->addSuccess('補助科目を削除しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の補助科目が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/masters/sub-account-titles');
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
            'active_master'      => 'sub_account_titles',
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
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $titles = $this->accountTitleRepo->findAllByEntity($entityId);
        $titleOptions = array_map(
            static fn (AccountTitle $a): array => [
                'id'   => $a->id,
                'code' => $a->code,
                'name' => $a->name,
            ],
            $titles,
        );
        $data = array_merge(
            $this->commonViewData($mode === 'new' ? '補助科目の新規追加' : '補助科目の編集'),
            [
                'form_mode'       => $mode,
                'form_action'     => $formAction,
                'form_values'     => $values,
                'form_errors'     => $errors,
                'title_options'   => $titleOptions,
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID),
                'csrf_form_field' => self::CSRF_FORM_ID,
            ],
        );
        return HtmlResponse::of($status, $this->view->render('masters/sub-account-titles/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankValues(): array
    {
        return [
            'account_title_id' => '',
            'code'             => '',
            'name'             => '',
            'sort_order'       => '0',
            'is_active'        => '1',
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, string>
     */
    private static function valuesFromBody(array $body): array
    {
        return [
            'account_title_id' => MasterFormSupport::str($body, 'account_title_id'),
            'code'             => MasterFormSupport::str($body, 'code'),
            'name'             => MasterFormSupport::str($body, 'name'),
            'sort_order'       => (string) MasterFormSupport::int($body, 'sort_order', 0),
            'is_active'        => MasterFormSupport::bool($body, 'is_active') ? '1' : '0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function valuesFromEntity(SubAccountTitle $s): array
    {
        return [
            'account_title_id' => $s->accountTitleId,
            'code'             => $s->code,
            'name'             => $s->name,
            'sort_order'       => (string) $s->sortOrder,
            'is_active'        => $s->isActive ? '1' : '0',
        ];
    }
}
