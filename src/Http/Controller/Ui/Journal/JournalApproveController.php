<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Journal\ApproveJournalUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;

/**
 * POST /ui/journals/{id}/approve — promote a Draft / PendingApproval journal
 * to Approved.
 *
 * In the single-operator deployment the same user who created the journal will
 * normally also approve it; the email-based approval flow described in ADR-007
 * remains an alternative side channel. This controller exists so the operator
 * is not forced to drop into psql / mariadb to move a journal forward.
 *
 * The controller is intentionally thin: guard auth + entity selection,
 * validate CSRF, dispatch {@see ApproveJournalUseCase}, surface the outcome
 * via flash + redirect.
 */
final readonly class JournalApproveController
{
    public const CSRF_FORM_ID = 'ui_journal_approve';

    public function __construct(
        private ApproveJournalUseCase $approveJournal,
        private JournalRepositoryInterface $journals,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
    ) {
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');

            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::notFound('仕訳が見つかりません。');
        }

        $existing = $this->journals->findById($id);
        if ($existing === null || $existing->entityId !== $entityId) {
            return HtmlResponse::notFound('仕訳が見つかりません。');
        }

        $body = JournalFormSupport::parseForm($request);
        $submitted = JournalFormSupport::str($body, '_csrf');
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');

            return HtmlResponse::redirect('/ui/journals/'.$existing->id);
        }

        try {
            $this->approveJournal->execute($existing->id, $userId);
            $this->flash->addSuccess('仕訳を承認しました。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の仕訳が見つかりませんでした。');
        } catch (InvariantViolationException $e) {
            $this->flash->addError($this->translateInvariant($e));
        } catch (\Throwable $e) {
            $this->flash->addError('承認処理でエラーが発生しました: '.$e->getMessage());
        }

        return HtmlResponse::redirect('/ui/journals/'.$existing->id);
    }

    private function translateInvariant(InvariantViolationException $e): string
    {
        $ctx = $e->context();
        $invariant = is_string($ctx['invariant'] ?? null) ? $ctx['invariant'] : 'unknown';

        return match ($invariant) {
            'journal.cannot_approve_from_status' => 'ドラフト / 承認待ち以外の仕訳は承認できません。',
            default => '承認に失敗しました: '.$e->getMessage(),
        };
    }
}
