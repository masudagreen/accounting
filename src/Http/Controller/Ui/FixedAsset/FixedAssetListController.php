<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use Rucaro\Application\FixedAsset\ListFixedAssetsUseCase;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/fixed-assets — render every fixed asset owned by the current
 * entity. Supports a "include disposed" query toggle (default: hide).
 */
final readonly class FixedAssetListController
{
    public function __construct(
        private ListFixedAssetsUseCase $listAssets,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function invoke(ServerRequest $request): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $includeDisposed = $request->queryBool('includeDisposed') ?? false;

        try {
            $assets = $this->listAssets->execute($entityId, $includeDisposed);
        } catch (\Throwable $e) {
            $this->flash->addError('固定資産一覧の取得に失敗しました: ' . $e->getMessage());
            $assets = [];
        }

        $items = array_map(
            static fn (FixedAsset $a): array => [
                'id'               => $a->id,
                'assetCode'        => $a->assetCode,
                'assetName'        => $a->assetName,
                'categoryCode'     => $a->categoryCode,
                'acquisitionDate'  => $a->acquisitionDate->format('Y-m-d'),
                'serviceStartDate' => $a->serviceStartDate->format('Y-m-d'),
                'acquisitionCost'  => $a->acquisitionCost,
                'residualValue'    => $a->residualValue,
                'usefulLifeYears'  => $a->usefulLifeYears,
                'method'           => $a->method->value,
                'quantity'         => $a->quantity,
                'disposalDate'     => $a->disposalDate?->format('Y-m-d') ?? '',
                'isDisposed'       => $a->disposalDate !== null,
            ],
            $assets,
        );

        $data = [
            'page_title'           => '固定資産一覧',
            'active_nav'           => 'fixed_assets',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'entities'             => [],
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'       => $this->flash->consume(),
            'items'                => $items,
            'total'                => count($items),
            'include_disposed'     => $includeDisposed,
        ];
        unset($request);
        return HtmlResponse::ok($this->view->render('fixed_assets/list.html.tpl', $data));
    }
}
