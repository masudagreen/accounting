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
use Rucaro\Support\Web\UserDisplayLookup;

/**
 * GET /ui/journals/{id} — render the journal detail page. Always
 * read-only; mutable journals (draft) get an 編集 button in the header
 * that links to the standalone edit form at /ui/journals/{id}/edit.
 */
final readonly class JournalShowController
{
    public function __construct(
        private JournalRepositoryInterface $journals,
        private JournalUiContext $uiContext,
        private UserDisplayLookup $userDisplay,
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

        // Admin can edit/delete posted journals; non-admin sees the
        // historical "draft only" gate.
        $isAdmin = $this->session->isAdmin();
        $canEdit = $journal->statusEnum()->isMutable() || $isAdmin;
        $status = $journal->statusEnum();
        $canApprove = $status === \Rucaro\Domain\Journal\JournalStatus::Draft
            || $status === \Rucaro\Domain\Journal\JournalStatus::PendingApproval;
        $canPost = $status === \Rucaro\Domain\Journal\JournalStatus::Approved;

        // Show is read-only: edits happen on /ui/journals/{id}/edit.
        // Only the lookup tables needed for the read-only twin-table are
        // pulled (account_titles + creator name); the heavy form JSON
        // (sub_accounts / tax_defaults / account_titles_json) lives on the
        // edit page now.
        $accountTitles = $this->uiContext->accountTitlesForEntity($entityId);
        $formLines = array_map(self::lineToArray(...), $journal->lines);
        $linesBySide = JournalFormSupport::splitLinesBySide($formLines);

        $creatorNames = $this->userDisplay->displayNamesByIds([$journal->createdBy]);
        $accountNameById = [];
        foreach ($accountTitles as $a) {
            $accountNameById[$a['id']] = $a['name'];
        }

        $data = [
            'page_title' => '仕訳詳細',
            'active_nav' => 'journals',
            'csrf_logout_token' => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token' => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field' => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field' => EntitySwitchController::CSRF_FORM_ID,
            'csrf_approve_token' => $canApprove ? $this->csrf->generateToken(JournalApproveController::CSRF_FORM_ID) : '',
            'csrf_post_token' => $canPost ? $this->csrf->generateToken(JournalPostController::CSRF_FORM_ID) : '',
            'display_name' => $this->session->getDisplayName() ?? '',
            'user_email' => $this->session->getEmail() ?? '',
            'entities' => $this->uiContext->entitiesForUser((string) $this->session->getUserId()),
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages' => $this->flash->consume(),
            'form_journal' => self::journalToArray($journal),
            'form_lines' => $formLines,
            'form_credit_lines' => $linesBySide['credit'],
            'form_debit_lines' => $linesBySide['debit'],
            'form_errors' => [],
            'account_titles' => $accountTitles,
            'fiscal_terms' => $this->uiContext->fiscalTermsForEntity($entityId),
            'creator_name' => $creatorNames[$journal->createdBy] ?? '',
            'account_name_by_id' => $accountNameById,
            'can_edit' => $canEdit,
            'can_approve' => $canApprove,
            'can_post' => $canPost,
        ];

        return HtmlResponse::ok($this->view->render('journals/show.html.tpl', $data));
    }

    /**
     * @return array{id: string, journalDate: string, summary: string, status: string, fiscalTermId: string, totalAmount: string, createdBy: string, createdAt: string}
     */
    private static function journalToArray(Journal $j): array
    {
        return [
            'id' => $j->id,
            'journalDate' => $j->journalDate->format('Y-m-d'),
            'summary' => $j->summary,
            'status' => $j->status,
            'fiscalTermId' => $j->fiscalTermId,
            'totalAmount' => JournalUiContext::formatAmount($j->totalAmount),
            'createdBy' => $j->createdBy,
            'createdAt' => $j->createdAt->format('Y-m-d H:i'),
        ];
    }

    /**
     * @return array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}
     */
    private static function lineToArray(JournalLine $line): array
    {
        return [
            'side' => $line->side,
            'account_title_id' => $line->accountTitleId,
            'sub_account_title_id' => $line->subAccountTitleId,
            'amount' => JournalUiContext::formatAmount($line->amount),
            'memo' => $line->memo,
            'tax_rate_percent' => JournalUiContext::formatTaxRatePercent($line->taxRatePercent),
            'tax_amount' => JournalUiContext::formatAmount($line->taxAmount),
            'is_tax_reduced' => $line->isTaxReduced,
        ];
    }
}
