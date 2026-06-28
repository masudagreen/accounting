<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Application\Journal\CreateJournalUseCase;
use Rucaro\Application\Journal\CreateJournalUseCaseInput;
use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET  /ui/journals/new   — render a fresh journal form.
 * POST /ui/journals/new   — validate, call {@see CreateJournalUseCase}, and
 *                           redirect to the journal detail page on success.
 *
 * All validation errors are rendered back into the same form with the
 * operator's values preserved so no work is lost on a balance-off mistake.
 */
final readonly class JournalNewController
{
    public const CSRF_FORM_ID = 'ui_journal_new';

    public function __construct(
        private CreateJournalUseCase $createJournal,
        private ListEntitiesUseCase $listEntities,
        private JournalUiContext $uiContext,
        private JournalRepositoryInterface $journals,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        // F-4 (1): the form must always boot with an entity selected so the
        // fiscal-term lookup has options. If the operator has not picked
        // one yet (fresh session, navigated here directly), grab the first
        // entity they own and persist it to the session.
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $entityId = $this->autoSelectEntity();
            if ($entityId === null) {
                $this->flash->addWarning('先に事業者（entity）を作成・選択してください。');

                return HtmlResponse::redirect('/ui/dashboard');
            }
            $this->session->setSelectedEntity($entityId);
        }

        $fiscalTerms = $this->uiContext->fiscalTermsForEntity($entityId);
        $activeTermId = $this->resolveActiveTermId($fiscalTerms);

        $today = $this->clock->getCurrentTime()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d');

        // ?duplicate_from=ID — copy the source journal's lines + summary,
        // keep today's date, ignore source's date / status. The button
        // lives on the list page; drafts and posted both can be a source.
        $formLines = [];
        $formSummary = '';
        $duplicateFrom = $request->queryString('duplicate_from');
        if ($duplicateFrom !== null && $duplicateFrom !== '') {
            $src = $this->journals->findById($duplicateFrom);
            if ($src !== null && $src->entityId === $entityId) {
                $formSummary = $src->summary;
                foreach ($src->lines as $l) {
                    $formLines[] = [
                        'side' => $l->side,
                        'account_title_id' => $l->accountTitleId,
                        'sub_account_title_id' => $l->subAccountTitleId,
                        'amount' => $l->amount,
                        'memo' => $l->memo,
                        'tax_rate_percent' => '0.00',
                        'tax_amount' => '0.0000',
                        'is_tax_reduced' => false,
                    ];
                }
            }
        }

        return $this->renderForm(
            entityId: $entityId,
            fiscalTerms: $fiscalTerms,
            activeFiscalTermId: $activeTermId,
            formJournalDate: $today,
            formSummary: $formSummary,
            formLines: $formLines,
            formErrors: [],
            status: 200,
        );
    }

    /**
     * Pick the fiscal term to default the form to. Reuses the navbar's
     * session selection only if it actually belongs to the current entity;
     * otherwise falls back to the entity's default term. Stale session
     * values (e.g. left over from a previous entity) would otherwise be
     * round-tripped through the form's hidden input and persisted as a
     * cross-entity row.
     *
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     */
    private function resolveActiveTermId(array $fiscalTerms): ?string
    {
        $sessionTermId = $this->session->getSelectedFiscalTerm();
        if ($sessionTermId !== null && self::termBelongsToEntity($sessionTermId, $fiscalTerms)) {
            return $sessionTermId;
        }

        return JournalUiContext::defaultFiscalTermId($fiscalTerms, $this->clock->getCurrentTime());
    }

    /**
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     */
    private static function termBelongsToEntity(string $termId, array $fiscalTerms): bool
    {
        if ($termId === '') {
            return false;
        }
        foreach ($fiscalTerms as $t) {
            if ($t['id'] === $termId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pick the first owned entity for the current user, or null when the
     * user owns none. Failures fall back to null silently — the caller
     * decides how to surface the empty case.
     */
    private function autoSelectEntity(): ?string
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return null;
        }
        try {
            $entities = $this->listEntities->execute(new ListEntitiesUseCaseInput(
                ownerUserId: $userId,
                page: 1,
                pageSize: 1,
                search: null,
                isActive: true,
            ));
        } catch (\Throwable) {
            return null;
        }

        return $entities->items === [] ? null : $entities->items[0]->id;
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $entityId = $this->autoSelectEntity();
            if ($entityId === null) {
                $this->flash->addWarning('先に事業者（entity）を作成・選択してください。');

                return HtmlResponse::redirect('/ui/dashboard');
            }
            $this->session->setSelectedEntity($entityId);
        }
        /** @var string $userId */
        $userId = $this->session->getUserId();

        $body = JournalFormSupport::parseForm($request);
        $submitted = JournalFormSupport::str($body, '_csrf');
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');

            return HtmlResponse::redirect('/ui/journals/new');
        }

        $journalDateRaw = JournalFormSupport::str($body, 'journal_date');
        $summary = JournalFormSupport::str($body, 'summary');
        $fiscalTermId = JournalFormSupport::str($body, 'fiscal_term_id');
        // Skip-approval is admin-only. Non-admins always get the historical
        // draft path even if they hand-craft a `skip_approval=1` POST body.
        $skipApproval = $this->session->isAdmin()
            && JournalFormSupport::str($body, 'skip_approval') === '1';
        // F-4: back to the flat `lines[N][side|...]` schema. Each table on
        // the form (credit-left / debit-right) emits its own rows; controllers
        // pull them via extractLines() exactly like before F-2.
        $rawLines = JournalFormSupport::extractLines($body);

        $fiscalTerms = $this->uiContext->fiscalTermsForEntity($entityId);
        // F-4 hdr fix: never trust the form's fiscal_term_id alone. The
        // form now ships it as a hidden input round-tripped from the
        // navbar selection, but the navbar value can be stale (e.g. left
        // over from a previous entity). Validate it belongs to the
        // CURRENT entity; fall back to the default term otherwise so we
        // never persist a cross-entity (entity_id, fiscal_term_id) pair.
        if ($fiscalTermId === '' || !self::termBelongsToEntity($fiscalTermId, $fiscalTerms)) {
            $fiscalTermId = JournalUiContext::defaultFiscalTermId($fiscalTerms, $this->clock->getCurrentTime()) ?? '';
        }

        $errors = [];
        $journalDate = null;
        try {
            $journalDate = new \DateTimeImmutable($journalDateRaw !== '' ? $journalDateRaw : 'now', new \DateTimeZone('UTC'));
        } catch (\Exception) {
            $errors['journal_date'] = ['発生日は YYYY-MM-DD 形式で入力してください。'];
        }
        if ($fiscalTermId === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        // F-4 (6): summary is now optional — operators logging quick recurring
        // entries shouldn't be forced to type a 摘要 every time. The use case
        // already accepts an empty string and the show/list templates render
        // a placeholder when missing.
        if (count($rawLines) < 2) {
            $errors['lines'] = ['借方と貸方をそれぞれ 1 行以上入力してください。'];
        }

        if ($errors === [] && $journalDate instanceof \DateTimeImmutable) {
            /** @var list<JournalLineInput> $lineInputs */
            $lineInputs = [];
            foreach ($rawLines as $raw) {
                $lineInputs[] = JournalFormSupport::toLineInput($raw);
            }
            try {
                $journal = $this->createJournal->execute(new CreateJournalUseCaseInput(
                    entityId: $entityId,
                    fiscalTermId: $fiscalTermId,
                    journalDate: $journalDate,
                    summary: $summary,
                    source: 'manual',
                    sourceReceiptId: null,
                    currencyCode: 'JPY',
                    createdBy: $userId,
                    skipApproval: $skipApproval,
                    lines: $lineInputs,
                ));
                $this->flash->addSuccess('仕訳を保存しました。');

                return HtmlResponse::redirect('/ui/journals/'.$journal->id);
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (InvariantViolationException $e) {
                $errors['_'] = [$this->translateInvariant($e)];
            } catch (\Throwable $e) {
                $errors['_'] = ['内部エラーが発生しました: '.$e->getMessage()];
            }
        }

        return $this->renderForm(
            entityId: $entityId,
            fiscalTerms: $fiscalTerms,
            activeFiscalTermId: $fiscalTermId !== '' ? $fiscalTermId : null,
            formJournalDate: $journalDateRaw,
            formSummary: $summary,
            formLines: $rawLines,
            formErrors: $errors,
            status: 422,
        );
    }

    /**
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     * @param list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}> $formLines passed empty for a fresh form, populated only when re-rendering after a 422
     * @param array<string, list<string>> $formErrors
     */
    private function renderForm(
        string $entityId,
        array $fiscalTerms,
        ?string $activeFiscalTermId,
        string $formJournalDate,
        string $formSummary,
        array $formLines,
        array $formErrors,
        int $status,
    ): HtmlResponse {
        $accountTitles = $this->uiContext->accountTitlesForEntity($entityId);
        $subByAccount = $this->uiContext->subAccountTitlesGroupedByAccountForEntity($entityId);
        $taxDefaults = $this->uiContext->consumptionTaxDefaultsForEntity($entityId);

        $linesBySide = JournalFormSupport::splitLinesBySide($formLines);

        $data = [
            'page_title' => '新規仕訳',
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
            'form_mode' => 'new',
            'form_action' => '/ui/journals/new',
            'form_journal' => [
                'id' => '',
                'journalDate' => $formJournalDate,
                'summary' => $formSummary,
                'status' => 'draft',
                'fiscalTermId' => $activeFiscalTermId ?? '',
            ],
            'form_lines' => $formLines,
            'form_credit_lines' => $linesBySide['credit'],
            'form_debit_lines' => $linesBySide['debit'],
            'form_errors' => $formErrors,
            'account_titles' => $accountTitles,
            'account_titles_json' => json_encode(
                $accountTitles,
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '[]',
            'sub_accounts_by_account_json' => json_encode(
                $subByAccount,
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '{}',
            'tax_defaults_json' => json_encode(
                $taxDefaults,
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ) ?: '{}',
            'fiscal_terms' => $fiscalTerms,
            'can_edit' => true,
            'is_admin' => $this->session->isAdmin(),
        ];

        return HtmlResponse::of($status, $this->view->render('journals/form.html.tpl', $data));
    }

    private function translateInvariant(InvariantViolationException $e): string
    {
        $ctx = $e->context();
        $invariant = is_string($ctx['invariant'] ?? null) ? $ctx['invariant'] : 'unknown';

        return match ($invariant) {
            'journal.must_balance' => '借方と貸方の合計が一致しません。',
            'journal.must_have_debit' => '借方の行が 1 行以上必要です。',
            'journal.must_have_credit' => '貸方の行が 1 行以上必要です。',
            'journal.min_lines' => '少なくとも 2 行（借方 1・貸方 1）が必要です。',
            default => '保存時にエラーが発生しました: '.$e->getMessage(),
        };
    }
}
