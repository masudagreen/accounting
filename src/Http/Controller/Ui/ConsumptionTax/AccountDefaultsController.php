<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\ListAccountTitleTaxDefaultsUseCase;
use Rucaro\Application\ConsumptionTax\UpsertAccountTitleTaxDefaultsUseCase;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/consumption-tax/account-defaults — bulk-edit screen that maps
 *                                              each account title to a
 *                                              default tax category.
 * POST /ui/consumption-tax/account-defaults
 */
final readonly class AccountDefaultsController
{
    public const CSRF_FORM_ID = 'ui_consumption_tax_account_defaults';

    public function __construct(
        private ListAccountTitleTaxDefaultsUseCase $listDefaults,
        private UpsertAccountTitleTaxDefaultsUseCase $upsert,
        private PlanningUiContext $ctx,
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
        return $this->render($entityId, 200);
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
            return HtmlResponse::redirect('/ui/consumption-tax/account-defaults');
        }

        $rows = $body['rows'] ?? null;
        /** @var list<array{accountTitleId: string, categoryCode: string, rateCode?: ?string}> $payload */
        $payload = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $aid = PlanningFormSupport::str($row, 'account_title_id');
                $cat = PlanningFormSupport::str($row, 'category_code');
                $rate = PlanningFormSupport::nullableStr($row, 'rate_code');
                if ($aid === '' || $cat === '') {
                    continue;
                }
                $payload[] = [
                    'accountTitleId' => $aid,
                    'categoryCode'   => $cat,
                    'rateCode'       => $rate,
                ];
            }
        }

        try {
            $this->upsert->execute($entityId, $payload);
            $this->flash->addSuccess('勘定科目 × 消費税区分の既定値を更新しました。');
        } catch (ValidationException $e) {
            $this->flash->addError('保存に失敗しました: ' . self::firstError($e));
        } catch (\Throwable $e) {
            $this->flash->addError('保存に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/consumption-tax/account-defaults');
    }

    private function render(string $entityId, int $status): HtmlResponse
    {
        $defaults = $this->listDefaults->execute($entityId);
        $byAccount = [];
        foreach ($defaults as $d) {
            $byAccount[$d->accountTitleId] = [
                'category' => $d->defaultCategoryCode->value,
                'rate'     => $d->defaultRateCode ?? '',
            ];
        }

        $accounts = $this->ctx->accountTitlesForEntity($entityId);
        $items = [];
        foreach ($accounts as $a) {
            $items[] = [
                'id'       => $a['id'],
                'code'     => $a['code'],
                'name'     => $a['name'],
                'category' => $byAccount[$a['id']]['category'] ?? '',
                'rate'     => $byAccount[$a['id']]['rate'] ?? '',
            ];
        }

        $data = [
            'page_title'           => '消費税区分の既定値',
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
            'category_options'     => self::categoryOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('consumption_tax/account_defaults.html.tpl', $data));
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function categoryOptions(): array
    {
        $options = [['value' => '', 'label' => '（未設定）']];
        foreach (ConsumptionTaxCategoryCode::cases() as $c) {
            $options[] = ['value' => $c->value, 'label' => $c->value];
        }
        return $options;
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
