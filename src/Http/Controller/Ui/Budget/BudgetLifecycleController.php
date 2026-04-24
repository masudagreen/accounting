<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Budget;

use Rucaro\Application\Budget\ApproveBudgetUseCase;
use Rucaro\Application\Budget\DeleteBudgetUseCase;
use Rucaro\Application\Budget\LockBudgetUseCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/budgets/{id}/approve
 * POST /ui/budgets/{id}/lock
 * POST /ui/budgets/{id}/delete
 *
 * Lifecycle transitions; each method re-uses the same CSRF token id
 * {@see BudgetShowController::CSRF_FORM_ID} because the same form context
 * renders all three buttons.
 */
final readonly class BudgetLifecycleController
{
    public function __construct(
        private ApproveBudgetUseCase $approve,
        private LockBudgetUseCase $lock,
        private DeleteBudgetUseCase $delete,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function approveAction(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $userId = $this->guard($id);
        if ($userId instanceof HtmlResponse) {
            return $userId;
        }
        if (!$this->checkCsrf($request)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }
        try {
            $this->approve->execute($id, $userId);
            $this->flash->addSuccess('予算を承認しました。');
        } catch (ValidationException $e) {
            $this->flash->addError('承認に失敗しました: ' . self::firstError($e));
        } catch (InvariantViolationException $e) {
            $this->flash->addError('ドラフト以外は承認できません。');
        } catch (\Throwable $e) {
            $this->flash->addError('承認処理でエラーが発生しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/budgets/' . $id);
    }

    public function lockAction(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $userId = $this->guard($id);
        if ($userId instanceof HtmlResponse) {
            return $userId;
        }
        if (!$this->checkCsrf($request)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }
        try {
            $this->lock->execute($id);
            $this->flash->addSuccess('予算をロックしました。');
        } catch (ValidationException $e) {
            $this->flash->addError('ロックに失敗しました: ' . self::firstError($e));
        } catch (InvariantViolationException $e) {
            $this->flash->addError('承認済みの予算のみロック可能です。');
        } catch (\Throwable $e) {
            $this->flash->addError('ロック処理でエラーが発生しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/budgets/' . $id);
    }

    public function deleteAction(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $userId = $this->guard($id);
        if ($userId instanceof HtmlResponse) {
            return $userId;
        }
        if (!$this->checkCsrf($request)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/budgets/' . $id);
        }
        try {
            $this->delete->execute($id);
            $this->flash->addSuccess('予算を削除しました。');
            return HtmlResponse::redirect('/ui/budgets');
        } catch (InvariantViolationException) {
            $this->flash->addError('ドラフト以外の予算は削除できません。');
        } catch (\Throwable $e) {
            $this->flash->addError('削除に失敗しました: ' . $e->getMessage());
        }
        return HtmlResponse::redirect('/ui/budgets/' . $id);
    }

    /**
     * @return string|HtmlResponse ULID if caller is authenticated, redirect otherwise.
     */
    private function guard(string $id): string|HtmlResponse
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound();
        }
        return $userId;
    }

    private function checkCsrf(ServerRequest $request): bool
    {
        $body = PlanningFormSupport::parseForm($request);
        return $this->csrf->validateToken(BudgetShowController::CSRF_FORM_ID, PlanningFormSupport::str($body, '_csrf'));
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
