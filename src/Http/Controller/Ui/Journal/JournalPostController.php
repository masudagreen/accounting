<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Journal\PostJournalUseCase;
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
 * POST /ui/journals/{id}/post — finalise an Approved journal (Approved → Posted).
 *
 * Posting is the point at which the entry becomes immutable and starts to
 * appear in downstream reports (trial balance / ledger / PL / BS). Because
 * posting is irreversible (only {@see PostJournalUseCase} forward and a
 * separate reversing journal backward), the controller intentionally does
 * **not** offer a "post draft directly" shortcut — the operator must approve
 * first.
 *
 * Like {@see JournalApproveController}, the controller is a thin guard around
 * the use case.
 */
final readonly class JournalPostController
{
    public const CSRF_FORM_ID = 'ui_journal_post';

    public function __construct(
        private PostJournalUseCase $postJournal,
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
            $this->postJournal->execute($existing->id, $userId);
            $this->flash->addSuccess('仕訳を確定しました。帳票へ反映されます。');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の仕訳が見つかりませんでした。');
        } catch (InvariantViolationException $e) {
            $this->flash->addError($this->translateInvariant($e));
        } catch (\Throwable $e) {
            $this->flash->addError('確定処理でエラーが発生しました: '.$e->getMessage());
        }

        return HtmlResponse::redirect('/ui/journals/'.$existing->id);
    }

    private function translateInvariant(InvariantViolationException $e): string
    {
        $ctx = $e->context();
        $invariant = is_string($ctx['invariant'] ?? null) ? $ctx['invariant'] : 'unknown';

        return match ($invariant) {
            'journal.cannot_post_from_status' => '承認済み以外の仕訳は確定できません。先に承認してください。',
            'journal.post_requires_fiscal_term' => '会計期間が設定されていないため確定できません。',
            default => '確定に失敗しました: '.$e->getMessage(),
        };
    }
}
