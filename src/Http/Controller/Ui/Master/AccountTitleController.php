<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Master;

use Rucaro\Application\AccountTitle\CreateAccountTitleUseCase;
use Rucaro\Application\AccountTitle\CreateAccountTitleUseCaseInput;
use Rucaro\Application\AccountTitle\DeleteAccountTitleUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Application\AccountTitle\UpdateAccountTitleUseCase;
use Rucaro\Application\AccountTitle\UpdateAccountTitleUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * CRUD controller for the 勘定科目 master.
 *
 * Methods mounted on the Web kernel:
 *   - GET  /ui/masters/account-titles         → {@see self::list()}
 *   - GET  /ui/masters/account-titles/new     → {@see self::newShow()}
 *   - POST /ui/masters/account-titles/new     → {@see self::newSubmit()}
 *   - GET  /ui/masters/account-titles/{id}    → {@see self::editShow()}
 *   - POST /ui/masters/account-titles/{id}    → {@see self::editSubmit()}
 *   - POST /ui/masters/account-titles/{id}/delete → {@see self::delete()}
 */
final readonly class AccountTitleController
{
    public const CSRF_FORM_ID = 'ui_master_account_title';
    public const PAGE_SIZE = 500;

    public function __construct(
        private ListAccountTitlesUseCase $listUseCase,
        private CreateAccountTitleUseCase $createUseCase,
        private UpdateAccountTitleUseCase $updateUseCase,
        private DeleteAccountTitleUseCase $deleteUseCase,
        private AccountTitleRepositoryInterface $repo,
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

        $out = $this->listUseCase->execute(new ListAccountTitlesUseCaseInput(
            entityId: $entityId,
            page: 1,
            pageSize: self::PAGE_SIZE,
            category: null,
            isActive: null,
            search: null,
        ));
        $rows = array_map(
            static fn (AccountTitle $a): array => [
                'id'         => $a->id,
                'code'       => $a->code,
                'name'       => $a->name,
                'category'   => $a->category,
                'normalSide' => $a->normalSide,
                'parentId'   => $a->parentId,
                'sortOrder'  => $a->sortOrder,
                'isActive'   => $a->isActive,
            ],
            $out->items,
        );
        /** @var array<string, list<array{id: string, code: string, name: string, category: string, normalSide: string, parentId: ?string, sortOrder: int, isActive: bool}>> $grouped */
        $grouped = [];
        foreach (AccountTitle::CATEGORIES as $cat) {
            $grouped[$cat] = [];
        }
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }

        return HtmlResponse::ok($this->view->render('masters/account-titles/list.html.tpl', array_merge(
            $this->commonViewData('勘定科目マスタ'),
            [
                'rows'    => $rows,
                'grouped' => $grouped,
                'total'   => $out->total,
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
            formAction: '/ui/masters/account-titles/new',
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
            return HtmlResponse::redirect('/ui/masters/account-titles/new');
        }
        $values = self::valuesFromBody($body);
        try {
            $this->createUseCase->execute(new CreateAccountTitleUseCaseInput(
                entityId: $entityId,
                code: $values['code'],
                name: $values['name'],
                category: $values['category'],
                normalSide: $values['normal_side'],
                parentId: MasterFormSupport::optionalStr($values['parent_id']),
                sortOrder: (int) $values['sort_order'],
                isActive: $values['is_active'] === '1',
            ));
            $this->flash->addSuccess('勘定科目を登録しました。');
            return HtmlResponse::redirect('/ui/masters/account-titles');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/account-titles/new',
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/account-titles/new',
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
            $this->flash->addError('対象の勘定科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/account-titles');
        }
        return $this->renderForm(
            mode: 'edit',
            formAction: '/ui/masters/account-titles/' . $existing->id,
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
            return HtmlResponse::redirect('/ui/masters/account-titles/' . $id);
        }
        $values = self::valuesFromBody($body);
        try {
            $this->updateUseCase->execute(new UpdateAccountTitleUseCaseInput(
                id: $id,
                code: $values['code'],
                name: $values['name'],
                category: $values['category'],
                normalSide: $values['normal_side'],
                parentId: MasterFormSupport::optionalStr($values['parent_id']),
                sortOrder: (int) $values['sort_order'],
                isActive: $values['is_active'] === '1',
            ));
            $this->flash->addSuccess('勘定科目を更新しました。');
            return HtmlResponse::redirect('/ui/masters/account-titles');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の勘定科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/account-titles');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/account-titles/' . $id,
                values: $values,
                errors: $e->errors(),
                status: 422,
                editingId: $id,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/account-titles/' . $id,
                values: $values,
                errors: ['_' => ['更新に失敗しました: ' . $e->getMessage()]],
                status: 500,
                editingId: $id,
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
            $this->flash->addError('対象の勘定科目が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/account-titles');
        }
        $data = array_merge(
            $this->commonViewData('勘定科目の削除確認'),
            [
                'target'          => [
                    'id'       => $existing->id,
                    'code'     => $existing->code,
                    'name'     => $existing->name,
                    'category' => $existing->category,
                ],
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID . '_delete'),
            ],
        );
        return HtmlResponse::ok($this->view->render('masters/account-titles/delete-confirm.html.tpl', $data));
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
            return HtmlResponse::redirect('/ui/masters/account-titles');
        }
        try {
            $this->deleteUseCase->execute($id);
            $this->flash->addSuccess('勘定科目を削除しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の勘定科目が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/masters/account-titles');
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
            'active_master'      => 'account_titles',
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
            'categories'         => AccountTitle::CATEGORIES,
            'category_labels'    => [
                'asset'     => '資産',
                'liability' => '負債',
                'equity'    => '純資産',
                'revenue'   => '収益',
                'expense'   => '費用',
            ],
            'normal_sides'       => AccountTitle::NORMAL_SIDES,
            'normal_side_labels' => ['debit' => '借方', 'credit' => '貸方'],
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
        ?string $editingId = null,
    ): HtmlResponse {
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        $allTitles = $this->repo->findAllByEntity($entityId);
        $parentOptions = [];
        foreach ($allTitles as $t) {
            if ($editingId !== null && $t->id === $editingId) {
                continue;
            }
            $parentOptions[] = [
                'id'   => $t->id,
                'code' => $t->code,
                'name' => $t->name,
            ];
        }
        $data = array_merge(
            $this->commonViewData($mode === 'new' ? '勘定科目の新規追加' : '勘定科目の編集'),
            [
                'form_mode'       => $mode,
                'form_action'     => $formAction,
                'form_values'     => $values,
                'form_errors'     => $errors,
                'parent_options'  => $parentOptions,
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID),
                'csrf_form_field' => self::CSRF_FORM_ID,
                'editing_id'      => $editingId,
            ],
        );
        return HtmlResponse::of($status, $this->view->render('masters/account-titles/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankValues(): array
    {
        return [
            'code'        => '',
            'name'        => '',
            'category'    => 'asset',
            'normal_side' => 'debit',
            'parent_id'   => '',
            'sort_order'  => '0',
            'is_active'   => '1',
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, string>
     */
    private static function valuesFromBody(array $body): array
    {
        return [
            'code'        => MasterFormSupport::str($body, 'code'),
            'name'        => MasterFormSupport::str($body, 'name'),
            'category'    => MasterFormSupport::str($body, 'category', 'asset'),
            'normal_side' => MasterFormSupport::str($body, 'normal_side', 'debit'),
            'parent_id'   => MasterFormSupport::str($body, 'parent_id'),
            'sort_order'  => (string) MasterFormSupport::int($body, 'sort_order', 0),
            'is_active'   => MasterFormSupport::bool($body, 'is_active') ? '1' : '0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function valuesFromEntity(AccountTitle $a): array
    {
        return [
            'code'        => $a->code,
            'name'        => $a->name,
            'category'    => $a->category,
            'normal_side' => $a->normalSide,
            'parent_id'   => $a->parentId ?? '',
            'sort_order'  => (string) $a->sortOrder,
            'is_active'   => $a->isActive ? '1' : '0',
        ];
    }
}
