<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FixedAsset\CreateFixedAssetInput;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
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
 * GET  /ui/fixed-assets/new — render the "register asset" form.
 * POST /ui/fixed-assets/new — validate + dispatch
 *                              {@see CreateFixedAssetUseCase}.
 */
final readonly class FixedAssetNewController
{
    public const CSRF_FORM_ID = 'ui_fixed_asset_new';

    public function __construct(
        private CreateFixedAssetUseCase $createAsset,
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
        return $this->renderForm(self::blankForm(), [], 200);
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        /** @var string $userId */
        $userId = $this->session->getUserId();

        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/fixed-assets/new');
        }

        $form = [
            'assetCode'         => PlanningFormSupport::str($body, 'asset_code'),
            'assetName'         => PlanningFormSupport::str($body, 'asset_name'),
            'categoryCode'      => PlanningFormSupport::str($body, 'category_code'),
            'acquisitionDate'   => PlanningFormSupport::str($body, 'acquisition_date'),
            'serviceStartDate'  => PlanningFormSupport::str($body, 'service_start_date'),
            'acquisitionCost'   => PlanningFormSupport::str($body, 'acquisition_cost'),
            'residualValue'     => PlanningFormSupport::str($body, 'residual_value', '0'),
            'usefulLifeYears'   => PlanningFormSupport::str($body, 'useful_life_years'),
            'method'            => PlanningFormSupport::str($body, 'method', 'straight_line'),
            'quantity'          => PlanningFormSupport::str($body, 'quantity', '1'),
            'departmentCode'    => PlanningFormSupport::str($body, 'department_code'),
            'note'              => PlanningFormSupport::str($body, 'note'),
        ];
        $errors = [];

        if ($form['assetCode'] === '') {
            $errors['asset_code'] = ['資産コードを入力してください。'];
        }
        if ($form['assetName'] === '') {
            $errors['asset_name'] = ['資産名を入力してください。'];
        }
        if ($form['categoryCode'] === '') {
            $errors['category_code'] = ['区分を入力してください。'];
        }

        $acquisition = self::parseDate($form['acquisitionDate']);
        if ($acquisition === null) {
            $errors['acquisition_date'] = ['取得日は YYYY-MM-DD 形式で入力してください。'];
        }
        $serviceStart = self::parseDate($form['serviceStartDate'] !== '' ? $form['serviceStartDate'] : $form['acquisitionDate']);
        if ($serviceStart === null) {
            $errors['service_start_date'] = ['事業供用日は YYYY-MM-DD 形式で入力してください。'];
        }

        if ($errors === [] && $acquisition !== null && $serviceStart !== null) {
            try {
                $out = $this->createAsset->execute(new CreateFixedAssetInput(
                    entityId: $entityId,
                    assetCode: $form['assetCode'],
                    assetName: $form['assetName'],
                    categoryCode: $form['categoryCode'],
                    assetAccountTitleId: null,
                    accumulatedDepreciationAccountTitleId: null,
                    depreciationExpenseAccountTitleId: null,
                    acquisitionDate: $acquisition,
                    serviceStartDate: $serviceStart,
                    acquisitionCost: PlanningFormSupport::normalizeAmount($form['acquisitionCost']),
                    residualValue: PlanningFormSupport::normalizeAmount($form['residualValue']),
                    usefulLifeYears: (int) $form['usefulLifeYears'],
                    method: $form['method'],
                    quantity: max(1, (int) $form['quantity']),
                    departmentCode: $form['departmentCode'] === '' ? null : $form['departmentCode'],
                    note: $form['note'] === '' ? null : $form['note'],
                    createdBy: $userId,
                ));
                $this->flash->addSuccess('固定資産を登録しました。');
                return HtmlResponse::redirect('/ui/fixed-assets/' . $out->asset->id);
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (\Throwable $e) {
                $errors['_'] = ['登録に失敗しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm($form, $errors, 422);
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
    private function renderForm(array $form, array $errors, int $status): HtmlResponse
    {
        $data = [
            'page_title'           => '新規固定資産',
            'active_nav'           => 'fixed_assets',
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
            'form_mode'            => 'new',
            'form_action'          => '/ui/fixed-assets/new',
            'form'                 => $form,
            'form_errors'          => $errors,
            'method_options'       => self::methodOptions(),
        ];
        return HtmlResponse::of($status, $this->view->render('fixed_assets/form.html.tpl', $data));
    }

    /**
     * @return array<string, string>
     */
    private static function blankForm(): array
    {
        return [
            'assetCode'         => '',
            'assetName'         => '',
            'categoryCode'      => '',
            'acquisitionDate'   => '',
            'serviceStartDate'  => '',
            'acquisitionCost'   => '',
            'residualValue'     => '0',
            'usefulLifeYears'   => '',
            'method'            => 'straight_line',
            'quantity'          => '1',
            'departmentCode'    => '',
            'note'              => '',
        ];
    }

    private static function parseDate(string $raw): ?DateTimeImmutable
    {
        if ($raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function methodOptions(): array
    {
        return array_map(
            static fn (DepreciationMethod $m): array => [
                'value' => $m->value,
                'label' => self::methodLabel($m),
            ],
            DepreciationMethod::cases(),
        );
    }

    private static function methodLabel(DepreciationMethod $m): string
    {
        return match ($m) {
            DepreciationMethod::StraightLine         => '定額法 (straight_line)',
            DepreciationMethod::DecliningBalance     => '定率法 (declining_balance)',
            DepreciationMethod::DecliningBalance2007 => '定率法 2007',
            DepreciationMethod::DecliningBalance2012 => '定率法 2012',
            DepreciationMethod::DecliningBalance2016 => '定率法 2016',
            DepreciationMethod::OldStraightLine      => '旧定額法',
            DepreciationMethod::OldDecliningBalance  => '旧定率法',
            DepreciationMethod::OneShot              => '一括償却',
            DepreciationMethod::ThreeYearEqual       => '3 年均等',
            DepreciationMethod::None                 => '償却しない',
        };
    }
}
