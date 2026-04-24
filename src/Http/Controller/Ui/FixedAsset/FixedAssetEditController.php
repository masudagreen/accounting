<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use Rucaro\Application\FixedAsset\UpdateFixedAssetUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/fixed-assets/{id} — apply the edit form from
 * {@see FixedAssetShowController}.
 */
final readonly class FixedAssetEditController
{
    public function __construct(
        private UpdateFixedAssetUseCase $updateAsset,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        $body = PlanningFormSupport::parseForm($request);
        if (!$this->csrf->validateToken(FixedAssetShowController::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }

        /**
         * @var array{
         *     assetName?: string,
         *     categoryCode?: string,
         *     residualValue?: string,
         *     usefulLifeYears?: int,
         *     method?: string,
         *     quantity?: int,
         *     departmentCode?: ?string,
         *     note?: ?string,
         * } $patch
         */
        $patch = [];
        $assetName = PlanningFormSupport::str($body, 'asset_name');
        if ($assetName !== '') {
            $patch['assetName'] = $assetName;
        }
        $categoryCode = PlanningFormSupport::str($body, 'category_code');
        if ($categoryCode !== '') {
            $patch['categoryCode'] = $categoryCode;
        }
        $residual = PlanningFormSupport::str($body, 'residual_value');
        if ($residual !== '') {
            $patch['residualValue'] = PlanningFormSupport::normalizeAmount($residual);
        }
        $useful = PlanningFormSupport::str($body, 'useful_life_years');
        if ($useful !== '') {
            $patch['usefulLifeYears'] = (int) $useful;
        }
        $method = PlanningFormSupport::str($body, 'method');
        if ($method !== '') {
            $patch['method'] = $method;
        }
        $qty = PlanningFormSupport::str($body, 'quantity');
        if ($qty !== '') {
            $patch['quantity'] = max(1, (int) $qty);
        }
        $dept = PlanningFormSupport::nullableStr($body, 'department_code');
        $patch['departmentCode'] = $dept;
        $note = PlanningFormSupport::nullableStr($body, 'note');
        $patch['note'] = $note;

        try {
            $this->updateAsset->execute($id, $patch);
            $this->flash->addSuccess('固定資産を更新しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の固定資産が見つかりません。');
        } catch (ValidationException $e) {
            $this->flash->addError('入力内容に誤りがあります: ' . self::firstError($e));
        } catch (\Throwable $e) {
            $this->flash->addError('更新に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
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
