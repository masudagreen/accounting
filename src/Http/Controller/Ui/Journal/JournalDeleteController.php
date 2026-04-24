<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Journal\DeleteJournalUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
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
 * GET  /ui/journals/{id}/delete — render the "are you sure?" confirmation
 *                                 page that surfaces a preview of the
 *                                 journal plus a POST-only delete form.
 * POST /ui/journals/{id}/delete — dispatch {@see DeleteJournalUseCase} and
 *                                 redirect back to the list on success.
 *
 * Only drafts can be deleted; the domain raises an
 * {@see InvariantViolationException} otherwise, which we surface as a
 * flash-warned redirect back to the journal detail page.
 */
final readonly class JournalDeleteController
{
    public const CSRF_FORM_ID = 'ui_journal_delete';

    public function __construct(
        private DeleteJournalUseCase $deleteJournal,
        private JournalRepositoryInterface $journals,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request, string $id = ''): HtmlResponse
    {
        unset($request);
        $guard = $this->guard($id);
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var Journal $journal */
        $journal = $this->journals->findById($id);

        $data = [
            'page_title'         => '仕訳の削除確認',
            'active_nav'         => 'journals',
            'csrf_logout_token'  => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'  => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'  => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'  => EntitySwitchController::CSRF_FORM_ID,
            'csrf_form_token'    => $this->csrf->generateToken(self::CSRF_FORM_ID),
            'csrf_form_field'    => self::CSRF_FORM_ID,
            'display_name'       => $this->session->getDisplayName() ?? '',
            'user_email'         => $this->session->getEmail() ?? '',
            'entities'           => [],
            'selected_entity_id' => (string) $this->session->getSelectedEntity(),
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'     => $this->flash->consume(),
            'journal'            => [
                'id'          => $journal->id,
                'journalDate' => $journal->journalDate->format('Y-m-d'),
                'summary'     => $journal->summary,
                'status'      => $journal->status,
                'totalAmount' => $journal->totalAmount,
                'createdAt'   => $journal->createdAt->format('Y-m-d H:i'),
            ],
            'lines'              => array_map(self::lineToArray(...), $journal->lines),
        ];
        return HtmlResponse::ok($this->view->render('journals/delete-confirm.html.tpl', $data));
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
    {
        $guard = $this->guard($id);
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }

        $body = JournalFormSupport::parseForm($request);
        $submitted = JournalFormSupport::str($body, '_csrf');
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/journals/' . $id);
        }

        try {
            $this->deleteJournal->execute($id, (string) $this->session->getUserId());
            $this->flash->addSuccess('仕訳を削除しました。');
            return HtmlResponse::redirect('/ui/journals');
        } catch (EntityNotFoundException) {
            $this->flash->addError('対象の仕訳が見つかりませんでした。');
            return HtmlResponse::redirect('/ui/journals');
        } catch (InvariantViolationException $e) {
            $ctx = $e->context();
            $invariant = is_string($ctx['invariant'] ?? null) ? $ctx['invariant'] : 'unknown';
            $this->flash->addError(
                $invariant === 'journal.cannot_delete_non_draft'
                    ? 'ドラフト以外の仕訳は削除できません。'
                    : '削除に失敗しました: ' . $e->getMessage(),
            );
            return HtmlResponse::redirect('/ui/journals/' . $id);
        } catch (\Throwable $e) {
            $this->flash->addError('削除中にエラーが発生しました: ' . $e->getMessage());
            return HtmlResponse::redirect('/ui/journals/' . $id);
        }
    }

    private function guard(string $id): ?HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }
        if (!UlidGenerator::isValid($id)) {
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
        }
        $journal = $this->journals->findById($id);
        if ($journal === null || $journal->entityId !== $entityId) {
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
        }
        return null;
    }

    /**
     * @return array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}
     */
    private static function lineToArray(JournalLine $line): array
    {
        return [
            'side'                 => $line->side,
            'account_title_id'     => $line->accountTitleId,
            'sub_account_title_id' => $line->subAccountTitleId,
            'amount'               => $line->amount,
            'memo'                 => $line->memo,
        ];
    }
}
