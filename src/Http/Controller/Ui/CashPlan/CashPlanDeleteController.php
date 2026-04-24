<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\CashPlan;

use Rucaro\Application\CashPlan\DeleteCashPlanUseCase;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/cash-plans/{id}/delete — soft-delete a cash plan.
 */
final readonly class CashPlanDeleteController
{
    public function __construct(
        private DeleteCashPlanUseCase $delete,
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
        if (!$this->csrf->validateToken(CashPlanShowController::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'))) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/cash-plans/' . $id);
        }
        try {
            $this->delete->execute($id);
            $this->flash->addSuccess('資金繰り計画を削除しました。');
            return HtmlResponse::redirect('/ui/cash-plans');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
            return HtmlResponse::redirect('/ui/cash-plans/' . $id);
        }
    }
}
