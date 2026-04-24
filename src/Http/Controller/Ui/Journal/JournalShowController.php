<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

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
 * GET /ui/journals/{id} — render the journal detail page. When the journal
 * is still in a mutable state (`draft`) the template exposes an edit form
 * in-place; posted / approved / rejected entries render read-only.
 */
final readonly class JournalShowController
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private JournalUiContext $uiContext,
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
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1><p>仕訳が見つかりません。</p>');
        }

        $journal = $this->journals->findById($id);
        if ($journal === null || $journal->entityId !== $entityId) {
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1><p>仕訳が見つかりません。</p>');
        }

        $canEdit = $journal->statusEnum()->isMutable();
        $data = [
            'page_title'         => '仕訳詳細',
            'active_nav'         => 'journals',
            'csrf_logout_token'  => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'  => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'  => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'  => EntitySwitchController::CSRF_FORM_ID,
            'csrf_form_token'    => $canEdit ? $this->csrf->generateToken(JournalEditController::CSRF_FORM_ID) : '',
            'csrf_form_field'    => JournalEditController::CSRF_FORM_ID,
            'display_name'       => $this->session->getDisplayName() ?? '',
            'user_email'         => $this->session->getEmail() ?? '',
            'entities'           => [],
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'     => $this->flash->consume(),
            'form_mode'          => $canEdit ? 'edit' : 'view',
            'form_action'        => '/ui/journals/' . $journal->id,
            'form_journal'       => self::journalToArray($journal),
            'form_lines'         => array_map(self::lineToArray(...), $journal->lines),
            'form_errors'        => [],
            'account_titles'     => $this->uiContext->accountTitlesForEntity($entityId),
            'fiscal_terms'       => $this->uiContext->fiscalTermsForEntity($entityId),
            'can_edit'           => $canEdit,
        ];
        return HtmlResponse::ok($this->view->render('journals/show.html.tpl', $data));
    }

    /**
     * @return array{id: string, journalDate: string, summary: string, status: string, fiscalTermId: string, totalAmount: string, createdBy: string, createdAt: string}
     */
    private static function journalToArray(Journal $j): array
    {
        return [
            'id'           => $j->id,
            'journalDate'  => $j->journalDate->format('Y-m-d'),
            'summary'      => $j->summary,
            'status'       => $j->status,
            'fiscalTermId' => $j->fiscalTermId,
            'totalAmount'  => $j->totalAmount,
            'createdBy'    => $j->createdBy,
            'createdAt'    => $j->createdAt->format('Y-m-d H:i'),
        ];
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
