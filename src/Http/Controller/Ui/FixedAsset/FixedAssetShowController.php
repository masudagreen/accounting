<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use Rucaro\Application\FixedAsset\GetFixedAssetUseCase;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/fixed-assets/{id} — render a single fixed asset, its depreciation
 * schedule, and an edit form.
 */
final readonly class FixedAssetShowController
{
    public const CSRF_FORM_ID = 'ui_fixed_asset_edit';

    public function __construct(
        private GetFixedAssetUseCase $getAsset,
        private DepreciationScheduleRepositoryInterface $schedules,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound('固定資産が見つかりません。');
        }
        $asset = $this->getAsset->execute($id);
        if ($asset === null || $asset->entityId !== $entityId) {
            return HtmlResponse::notFound('固定資産が見つかりません。');
        }

        $schedules = $this->schedules->findByAsset($asset->id);
        usort(
            $schedules,
            static fn (DepreciationScheduleEntry $a, DepreciationScheduleEntry $b): int
                => $a->periodNumber <=> $b->periodNumber,
        );

        $data = [
            'page_title'           => '固定資産詳細',
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
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'asset'                => self::assetToArray($asset),
            'schedules'            => array_map(self::entryToArray(...), $schedules),
            'method_options'       => FixedAssetNewController::methodOptions(),
        ];
        return HtmlResponse::ok($this->view->render('fixed_assets/show.html.tpl', $data));
    }

    /**
     * @return array<string, mixed>
     */
    public static function assetToArray(FixedAsset $a): array
    {
        return [
            'id'               => $a->id,
            'assetCode'        => $a->assetCode,
            'assetName'        => $a->assetName,
            'categoryCode'     => $a->categoryCode,
            'acquisitionDate'  => $a->acquisitionDate->format('Y-m-d'),
            'serviceStartDate' => $a->serviceStartDate->format('Y-m-d'),
            'disposalDate'     => $a->disposalDate?->format('Y-m-d') ?? '',
            'acquisitionCost'  => $a->acquisitionCost,
            'residualValue'    => $a->residualValue,
            'usefulLifeYears'  => $a->usefulLifeYears,
            'method'           => $a->method->value,
            'quantity'         => $a->quantity,
            'departmentCode'   => $a->departmentCode ?? '',
            'note'             => $a->note ?? '',
            'isDisposed'       => $a->disposalDate !== null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function entryToArray(DepreciationScheduleEntry $e): array
    {
        return [
            'periodNumber'            => $e->periodNumber,
            'periodStartDate'         => $e->periodStartDate->format('Y-m-d'),
            'periodEndDate'           => $e->periodEndDate->format('Y-m-d'),
            'monthsInService'         => $e->monthsInService,
            'openingBookValue'        => $e->openingBookValue,
            'depreciationAmount'      => $e->depreciationAmount,
            'accumulatedDepreciation' => $e->accumulatedDepreciation,
            'closingBookValue'        => $e->closingBookValue,
            'isPosted'                => $e->isPosted,
        ];
    }
}
