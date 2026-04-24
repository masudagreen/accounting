<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Master;

use Rucaro\Application\Entity\CreateEntityUseCase;
use Rucaro\Application\Entity\CreateEntityUseCaseInput;
use Rucaro\Application\Entity\DeleteEntityUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Application\Entity\UpdateEntityUseCase;
use Rucaro\Application\Entity\UpdateEntityUseCaseInput;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
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
 * CRUD controller for 事業主 (entity) master.
 */
final readonly class EntityController
{
    public const CSRF_FORM_ID = 'ui_master_entity';
    public const PAGE_SIZE = 200;

    public function __construct(
        private ListEntitiesUseCase $listUseCase,
        private CreateEntityUseCase $createUseCase,
        private UpdateEntityUseCase $updateUseCase,
        private DeleteEntityUseCase $deleteUseCase,
        private EntityRepositoryInterface $repo,
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
        /** @var string $userId */
        $userId = $this->session->getUserId();
        $out = $this->listUseCase->execute(new ListEntitiesUseCaseInput(
            ownerUserId: $userId,
            page: 1,
            pageSize: self::PAGE_SIZE,
            search: null,
            isActive: null,
        ));
        $rows = array_map(
            static fn (Entity $e): array => [
                'id'              => $e->id,
                'name'            => $e->name,
                'nationCode'      => $e->nationCode,
                'currencyCode'    => $e->currencyCode,
                'fiscalStartMmDd' => $e->fiscalStartMmDd,
                'isActive'        => $e->isActive,
                'isCorporate'     => $e->isCorporate,
            ],
            $out->items,
        );
        return HtmlResponse::ok($this->view->render('masters/entities/list.html.tpl', array_merge(
            $this->commonViewData('事業主マスタ'),
            ['rows' => $rows, 'total' => $out->total],
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
            formAction: '/ui/masters/entities/new',
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
        /** @var string $userId */
        $userId = $this->session->getUserId();
        $body = MasterFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, MasterFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/masters/entities/new');
        }
        $values = self::valuesFromBody($body);
        try {
            $this->createUseCase->execute(new CreateEntityUseCaseInput(
                ownerUserId: $userId,
                name: $values['name'],
                nationCode: strtoupper($values['nation_code']),
                currencyCode: strtoupper($values['currency_code']),
                fiscalStartMmDd: $values['fiscal_start_mmdd'],
                isActive: $values['is_active'] === '1',
                isCorporate: $values['is_corporate'] === '1',
            ));
            $this->flash->addSuccess('事業主を登録しました。');
            return HtmlResponse::redirect('/ui/masters/entities');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/entities/new',
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'new',
                formAction: '/ui/masters/entities/new',
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
            $this->flash->addError('対象の事業主が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/entities');
        }
        return $this->renderForm(
            mode: 'edit',
            formAction: '/ui/masters/entities/' . $existing->id,
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
            return HtmlResponse::redirect('/ui/masters/entities/' . $id);
        }
        $values = self::valuesFromBody($body);
        try {
            $this->updateUseCase->execute(new UpdateEntityUseCaseInput(
                id: $id,
                name: $values['name'],
                nationCode: strtoupper($values['nation_code']),
                currencyCode: strtoupper($values['currency_code']),
                fiscalStartMmDd: $values['fiscal_start_mmdd'],
                isActive: $values['is_active'] === '1',
                isCorporate: $values['is_corporate'] === '1',
            ));
            $this->flash->addSuccess('事業主を更新しました。');
            return HtmlResponse::redirect('/ui/masters/entities');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の事業主が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/entities');
        } catch (ValidationException $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/entities/' . $id,
                values: $values,
                errors: $e->errors(),
                status: 422,
            );
        } catch (\Throwable $e) {
            return $this->renderForm(
                mode: 'edit',
                formAction: '/ui/masters/entities/' . $id,
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
            $this->flash->addError('対象の事業主が見つかりません。');
            return HtmlResponse::redirect('/ui/masters/entities');
        }
        $data = array_merge(
            $this->commonViewData('事業主の削除確認'),
            [
                'target' => [
                    'id'           => $existing->id,
                    'name'         => $existing->name,
                    'nationCode'   => $existing->nationCode,
                    'currencyCode' => $existing->currencyCode,
                ],
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID . '_delete'),
            ],
        );
        return HtmlResponse::ok($this->view->render('masters/entities/delete-confirm.html.tpl', $data));
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
            return HtmlResponse::redirect('/ui/masters/entities');
        }
        try {
            $this->deleteUseCase->execute($id);
            $this->flash->addSuccess('事業主を削除しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の事業主が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/masters/entities');
    }

    private function guard(): ?HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
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
            'active_master'      => 'entities',
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
            $this->commonViewData($mode === 'new' ? '事業主の新規追加' : '事業主の編集'),
            [
                'form_mode'       => $mode,
                'form_action'     => $formAction,
                'form_values'     => $values,
                'form_errors'     => $errors,
                'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID),
                'csrf_form_field' => self::CSRF_FORM_ID,
            ],
        );
        return HtmlResponse::of($status, $this->view->render('masters/entities/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankValues(): array
    {
        return [
            'name'              => '',
            'nation_code'       => 'JPN',
            'currency_code'     => 'JPY',
            'fiscal_start_mmdd' => '0101',
            'is_active'         => '1',
            'is_corporate'      => '1',
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, string>
     */
    private static function valuesFromBody(array $body): array
    {
        return [
            'name'              => MasterFormSupport::str($body, 'name'),
            'nation_code'       => MasterFormSupport::str($body, 'nation_code', 'JPN'),
            'currency_code'     => MasterFormSupport::str($body, 'currency_code', 'JPY'),
            'fiscal_start_mmdd' => MasterFormSupport::str($body, 'fiscal_start_mmdd', '0101'),
            'is_active'         => MasterFormSupport::bool($body, 'is_active') ? '1' : '0',
            'is_corporate'      => MasterFormSupport::bool($body, 'is_corporate') ? '1' : '0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function valuesFromEntity(Entity $e): array
    {
        return [
            'name'              => $e->name,
            'nation_code'       => $e->nationCode,
            'currency_code'     => $e->currencyCode,
            'fiscal_start_mmdd' => $e->fiscalStartMmDd,
            'is_active'         => $e->isActive ? '1' : '0',
            'is_corporate'      => $e->isCorporate ? '1' : '0',
        ];
    }
}
