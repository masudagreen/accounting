<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\DeleteSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/ss-adjustments/{id}/delete — hard-delete a manual adjustment.
 */
final readonly class SsAdjustmentDeleteController
{
    public function __construct(
        private DeleteSsAdjustmentUseCase $delete,
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
        if (!$this->csrf->validateToken(SsAdjustmentEditController::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/ss-adjustments/' . $id);
        }
        try {
            $this->delete->execute($id);
            $this->flash->addSuccess('純資産変動調整を削除しました。');
            return HtmlResponse::redirect('/ui/ss-adjustments');
        } catch (ValidationException) {
            $this->flash->addError('対象の調整が見つかりません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/ss-adjustments/' . $id);
    }
}
