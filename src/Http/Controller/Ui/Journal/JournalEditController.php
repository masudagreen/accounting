<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Application\Journal\UpdateJournalUseCase;
use Rucaro\Application\Journal\UpdateJournalUseCaseInput;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
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
 * GET  /ui/journals/{id}/edit — render the standalone edit form for a
 *                               mutable journal (same chrome as the
 *                               new-journal page; values pre-populated).
 * POST /ui/journals/{id}      — apply the edit. The aggregate enforces
 *                               the "only Draft is mutable" rule, so the
 *                               controller is thin: collect form fields,
 *                               dispatch to {@see UpdateJournalUseCase},
 *                               and on success redirect to the read-only
 *                               detail page (or re-render the edit form
 *                               with inline errors on validation fail).
 */
final readonly class JournalEditController
{
    public const CSRF_FORM_ID = 'ui_journal_edit';

    public function __construct(
        private UpdateJournalUseCase $updateJournal,
        private JournalRepositoryInterface $journals,
        private JournalUiContext $uiContext,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request, string $id = ''): HtmlResponse
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
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
        }
        $existing = $this->journals->findById($id);
        if ($existing === null || $existing->entityId !== $entityId) {
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
        }
        // Admin can edit posted journals too; non-admin sees the historical
        // mutability gate (only Draft).
        if (!$existing->statusEnum()->isMutable() && !$this->session->isAdmin()) {
            $this->flash->addWarning('この仕訳は編集できない状態です。');

            return HtmlResponse::redirect('/ui/journals/'.$existing->id);
        }

        $formLines = array_map(self::lineToArray(...), $existing->lines);

        return $this->renderEditForm(
            existing: $existing,
            entityId: $entityId,
            summary: $existing->summary,
            formLines: $formLines,
            errors: [],
            status: 200,
        );
    }

    public function submit(ServerRequest $request, string $id = ''): HtmlResponse
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

        $existing = $this->journals->findById($id);
        if ($existing === null || $existing->entityId !== $entityId) {
            return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
        }
        if (!$existing->statusEnum()->isMutable() && !$this->session->isAdmin()) {
            $this->flash->addError('この仕訳は編集できない状態です。');

            return HtmlResponse::redirect('/ui/journals/'.$existing->id);
        }

        $body = JournalFormSupport::parseForm($request);
        $submitted = JournalFormSupport::str($body, '_csrf');
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');

            return HtmlResponse::redirect('/ui/journals/'.$existing->id);
        }

        $summary = JournalFormSupport::str($body, 'summary');
        // F-4: form posts the flat `lines[N][side|...]` schema again (the
        // pair-shaped F-2 wire format was reverted along with the template).
        $rawLines = JournalFormSupport::extractLines($body);

        $errors = [];
        if (count($rawLines) < 2) {
            $errors['lines'] = ['借方と貸方をそれぞれ 1 行以上入力してください。'];
        }

        if ($errors === []) {
            /** @var list<JournalLineInput> $lineInputs */
            $lineInputs = [];
            foreach ($rawLines as $raw) {
                $lineInputs[] = JournalFormSupport::toLineInput($raw);
            }
            try {
                $this->updateJournal->execute(new UpdateJournalUseCaseInput(
                    journalId: $existing->id,
                    updatedBy: (string) $this->session->getUserId(),
                    lines: $lineInputs,
                    summary: $summary !== '' ? $summary : null,
                    bypassMutabilityCheck: $this->session->isAdmin(),
                ));
                $this->flash->addSuccess('仕訳を更新しました。');

                return HtmlResponse::redirect('/ui/journals/'.$existing->id);
            } catch (EntityNotFoundException) {
                return HtmlResponse::of(404, '<!doctype html><meta charset="utf-8"><title>404</title><h1>404</h1>');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (InvariantViolationException $e) {
                $errors['_'] = [$this->translateInvariant($e)];
            } catch (\Throwable $e) {
                $errors['_'] = ['内部エラーが発生しました: '.$e->getMessage()];
            }
        }

        $rawLinesForRender = $rawLines !== []
            ? $rawLines
            : array_map(self::lineToArray(...), $existing->lines);

        return $this->renderEditForm(
            existing: $existing,
            entityId: $entityId,
            summary: $summary !== '' ? $summary : $existing->summary,
            formLines: $rawLinesForRender,
            errors: $errors,
            status: 422,
        );
    }

    /**
     * @param list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}> $formLines
     * @param array<string, list<string>> $errors
     */
    private function renderEditForm(
        Journal $existing,
        string $entityId,
        string $summary,
        array $formLines,
        array $errors,
        int $status,
    ): HtmlResponse {
        $linesBySide = JournalFormSupport::splitLinesBySide($formLines);
        $accountTitles = $this->uiContext->accountTitlesForEntity($entityId);

        $data = [
            'page_title' => '仕訳編集',
            'active_nav' => 'journals',
            'csrf_logout_token' => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token' => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field' => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field' => EntitySwitchController::CSRF_FORM_ID,
            'csrf_form_token' => $this->csrf->generateToken(self::CSRF_FORM_ID),
            'csrf_form_field' => self::CSRF_FORM_ID,
            'display_name' => $this->session->getDisplayName() ?? '',
            'user_email' => $this->session->getEmail() ?? '',
            'entities' => $this->uiContext->entitiesForUser((string) $this->session->getUserId()),
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages' => $this->flash->consume(),
            'form_mode' => 'edit',
            'form_action' => '/ui/journals/'.$existing->id,
            'form_journal' => [
                'id' => $existing->id,
                'journalDate' => $existing->journalDate->format('Y-m-d'),
                'summary' => $summary,
                'status' => $existing->status,
                'fiscalTermId' => $existing->fiscalTermId,
                'totalAmount' => JournalUiContext::formatAmount($existing->totalAmount),
                'createdBy' => $existing->createdBy,
                'createdAt' => $existing->createdAt->format('Y-m-d H:i'),
            ],
            'form_lines' => $formLines,
            'form_credit_lines' => $linesBySide['credit'],
            'form_debit_lines' => $linesBySide['debit'],
            'form_errors' => $errors,
            'account_titles' => $accountTitles,
            'account_titles_json' => json_encode(
                $accountTitles,
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '[]',
            'sub_accounts_by_account_json' => json_encode(
                $this->uiContext->subAccountTitlesGroupedByAccountForEntity($entityId),
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '{}',
            'tax_defaults_json' => json_encode(
                $this->uiContext->consumptionTaxDefaultsForEntity($entityId),
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '{}',
            'fiscal_terms' => $this->uiContext->fiscalTermsForEntity($entityId),
            'can_edit' => true,
        ];

        return HtmlResponse::of($status, $this->view->render('journals/form.html.tpl', $data));
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

    private function translateInvariant(InvariantViolationException $e): string
    {
        $ctx = $e->context();
        $invariant = is_string($ctx['invariant'] ?? null) ? $ctx['invariant'] : 'unknown';

        return match ($invariant) {
            'journal.must_balance' => '借方と貸方の合計が一致しません。',
            'journal.must_have_debit' => '借方の行が 1 行以上必要です。',
            'journal.must_have_credit' => '貸方の行が 1 行以上必要です。',
            'journal.immutable_after_draft' => 'この仕訳はドラフト以外のため編集できません。',
            default => '更新時にエラーが発生しました: '.$e->getMessage(),
        };
    }
}
