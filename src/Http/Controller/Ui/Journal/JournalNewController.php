<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\Journal\CreateJournalUseCase;
use Rucaro\Application\Journal\CreateJournalUseCaseInput;
use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
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
        private JournalUiContext $uiContext,
        private ClockInterface $clock,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function show(ServerRequest $request): HtmlResponse
    {
        unset($request);
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();

        $fiscalTerms = $this->uiContext->fiscalTermsForEntity($entityId);
        $activeTermId = $this->session->getSelectedFiscalTerm()
            ?? JournalUiContext::defaultFiscalTermId($fiscalTerms, $this->clock->getCurrentTime());

        $today = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');

        return $this->renderForm(
            entityId: $entityId,
            fiscalTerms: $fiscalTerms,
            activeFiscalTermId: $activeTermId,
            formJournalDate: $today,
            formSummary: '',
            formLines: self::blankLines(),
            formErrors: [],
            status: 200,
        );
    }

    public function submit(ServerRequest $request): HtmlResponse
    {
        $guard = $this->guard();
        if ($guard instanceof HtmlResponse) {
            return $guard;
        }
        /** @var string $entityId */
        $entityId = $this->session->getSelectedEntity();
        /** @var string $userId */
        $userId = $this->session->getUserId();

        $body = JournalFormSupport::parseForm($request);
        $submitted = JournalFormSupport::str($body, '_csrf');
        if (!$this->csrf->validateToken(self::CSRF_FORM_ID, $submitted)) {
            $this->flash->addError('セッションの有効期限が切れました。もう一度お試しください。');
            return HtmlResponse::redirect('/ui/journals/new');
        }

        $journalDateRaw = JournalFormSupport::str($body, 'journal_date');
        $summary        = JournalFormSupport::str($body, 'summary');
        $fiscalTermId   = JournalFormSupport::str($body, 'fiscal_term_id');
        $rawLines       = JournalFormSupport::extractLines($body);

        $fiscalTerms = $this->uiContext->fiscalTermsForEntity($entityId);
        if ($fiscalTermId === '') {
            $fiscalTermId = JournalUiContext::defaultFiscalTermId($fiscalTerms, $this->clock->getCurrentTime()) ?? '';
        }

        $errors = [];
        $journalDate = null;
        try {
            $journalDate = new DateTimeImmutable($journalDateRaw !== '' ? $journalDateRaw : 'now', new DateTimeZone('UTC'));
        } catch (\Exception) {
            $errors['journal_date'] = ['発生日は YYYY-MM-DD 形式で入力してください。'];
        }
        if ($fiscalTermId === '') {
            $errors['fiscal_term_id'] = ['会計期間を選択してください。'];
        }
        if ($summary === '') {
            $errors['summary'] = ['摘要を入力してください。'];
        }
        if (count($rawLines) < 2) {
            $errors['lines'] = ['借方と貸方をそれぞれ 1 行以上入力してください。'];
        }

        if ($errors === [] && $journalDate instanceof DateTimeImmutable) {
            /** @var list<JournalLineInput> $lineInputs */
            $lineInputs = [];
            foreach ($rawLines as $raw) {
                $lineInputs[] = new JournalLineInput(
                    side: $raw['side'],
                    accountTitleId: $raw['account_title_id'],
                    subAccountTitleId: $raw['sub_account_title_id'],
                    amount: JournalFormSupport::normalizeAmount($raw['amount']),
                    taxRatePercent: '0.00',
                    taxAmount: '0.0000',
                    isTaxReduced: false,
                    memo: $raw['memo'],
                );
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
                    lines: $lineInputs,
                ));
                $this->flash->addSuccess('仕訳を保存しました。');
                return HtmlResponse::redirect('/ui/journals/' . $journal->id);
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->errors());
            } catch (InvariantViolationException $e) {
                $errors['_'] = [$this->translateInvariant($e)];
            } catch (\Throwable $e) {
                $errors['_'] = ['内部エラーが発生しました: ' . $e->getMessage()];
            }
        }

        return $this->renderForm(
            entityId: $entityId,
            fiscalTerms: $fiscalTerms,
            activeFiscalTermId: $fiscalTermId !== '' ? $fiscalTermId : null,
            formJournalDate: $journalDateRaw,
            formSummary: $summary,
            formLines: self::rehydrateLines($rawLines),
            formErrors: $errors,
            status: 422,
        );
    }

    private function guard(): ?HtmlResponse
    {
        if ($this->session->getUserId() === null) {
            return HtmlResponse::redirect('/ui/login');
        }
        if ($this->session->getSelectedEntity() === null) {
            $this->flash->addWarning('先に事業者（entity）を選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }
        return null;
    }

    /**
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     * @param list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}> $formLines
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
        $data = [
            'page_title'         => '新規仕訳',
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
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'flash_messages'     => $this->flash->consume(),
            'form_mode'          => 'new',
            'form_action'        => '/ui/journals/new',
            'form_journal'       => [
                'id'           => '',
                'journalDate'  => $formJournalDate,
                'summary'      => $formSummary,
                'status'       => 'draft',
                'fiscalTermId' => $activeFiscalTermId ?? '',
            ],
            'form_lines'         => $formLines,
            'form_errors'        => $formErrors,
            'account_titles'     => $this->uiContext->accountTitlesForEntity($entityId),
            'fiscal_terms'       => $fiscalTerms,
            'can_edit'           => true,
        ];
        return HtmlResponse::of($status, $this->view->render('journals/form.html.tpl', $data));
    }

    /**
     * @return list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}>
     */
    private static function blankLines(): array
    {
        return [
            ['side' => 'debit',  'account_title_id' => '', 'sub_account_title_id' => null, 'amount' => '', 'memo' => ''],
            ['side' => 'credit', 'account_title_id' => '', 'sub_account_title_id' => null, 'amount' => '', 'memo' => ''],
        ];
    }

    /**
     * @param list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}> $rawLines
     * @return list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}>
     */
    private static function rehydrateLines(array $rawLines): array
    {
        if ($rawLines === []) {
            return self::blankLines();
        }
        return $rawLines;
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
            default => '保存時にエラーが発生しました: ' . $e->getMessage(),
        };
    }
}
