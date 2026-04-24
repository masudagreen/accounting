<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FixedAsset\DisposeFixedAssetUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/fixed-assets/{id}/dispose — mark a fixed asset as disposed.
 */
final readonly class FixedAssetDisposeController
{
    public const CSRF_FORM_ID = 'ui_fixed_asset_dispose';

    public function __construct(
        private DisposeFixedAssetUseCase $disposeAsset,
        private ClockInterface $clock,
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
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }

        $raw = PlanningFormSupport::str($body, 'disposal_date');
        try {
            $at = $raw !== '' ? new DateTimeImmutable($raw, new DateTimeZone('UTC')) : $this->clock->getCurrentTime();
        } catch (\Exception) {
            $this->flash->addError('除却日は YYYY-MM-DD 形式で入力してください。');
            return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
        }
        try {
            $this->disposeAsset->execute($id, $at);
            $this->flash->addSuccess('固定資産を除却しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の固定資産が見つかりません。');
        } catch (ValidationException $e) {
            $this->flash->addError('除却処理に失敗しました: ' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->flash->addError('除却処理でエラーが発生しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/fixed-assets/' . $id);
    }
}
