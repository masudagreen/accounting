<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Application\Ledger\QueryLedgerUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Domain\Ledger\LedgerGeneratorInterface;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/ledger — renders the general ledger (総勘定元帳) for the currently
 * selected entity + fiscal term.
 *
 * Query params:
 *   - accountTitleId  (optional) — when provided only that book is rendered.
 *                                  When absent every book for the entity is
 *                                  shown with a selector pointing at the first.
 *   - year, month     (optional) — see {@see PeriodQueryHelper} for the
 *                                  resolution rules.
 *   - format          (optional) — `pdf` re-renders the aggregate through the
 *                                  same {@see LedgerGeneratorInterface} the
 *                                  REST API uses, served behind the UI
 *                                  session auth so operators can download the
 *                                  PDF without juggling Bearer tokens.
 */
final readonly class LedgerViewController
{
    public function __construct(
        private QueryLedgerUseCase $queryLedger,
        private ListAccountTitlesUseCase $listAccountTitles,
        private LedgerGeneratorInterface $pdfGenerator,
        private PeriodQueryHelper $period,
        private SessionStore $session,
        private CsrfTokenManager $csrf,
        private FlashMessageBag $flash,
        private SmartyViewRenderer $view,
    ) {
    }

    public function __invoke(ServerRequest $request): HtmlResponse
    {
        $entityId = $this->session->getSelectedEntity();
        if ($entityId === null) {
            $this->flash->addError('会計単位 (entity) が未選択です。上部ナビから選択してください。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $fiscalTermId = $this->session->getSelectedFiscalTerm()
            ?? $this->period->findLatestFiscalTermId($entityId);
        if ($fiscalTermId === null) {
            $this->flash->addError('会計期 (fiscal_term) が登録されていません。');
            return HtmlResponse::redirect('/ui/dashboard');
        }

        $year  = PeriodQueryHelper::parseYear($request->queryString('year'));
        $month = PeriodQueryHelper::parseMonth($request->queryString('month'));
        [$from, $to, $termStart, $termEnd] = $this->period->resolve($fiscalTermId, $year, $month);

        $accountTitles = $this->listAccountTitles->execute(new ListAccountTitlesUseCaseInput(
            entityId: $entityId,
            page: 1,
            pageSize: 500,
            category: null,
            isActive: true,
            search: null,
        ))->items;

        $accountTitleId = $request->queryString('accountTitleId');
        if ($accountTitleId === '' || $accountTitleId === null) {
            $accountTitleId = null;
        }

        $ledger = $this->queryLedger->execute(new QueryLedgerUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            accountTitleId: $accountTitleId,
            fromDate: $from,
            toDate: $to,
        ))->ledger;

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf') {
            $pdf = $this->pdfGenerator->render($ledger);
            return new HtmlResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="ledger.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        $data = [
            'page_title'           => '総勘定元帳',
            'active_nav'           => 'ledger',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $fiscalTermId,
            'entities'             => [],
            'account_titles'       => array_map(self::accountTitleToArray(...), $accountTitles),
            'selected_account_title_id' => $accountTitleId ?? '',
            'year'                 => $year !== null ? (string) $year : '',
            'month'                => $month !== null ? (string) $month : '',
            'from_date'            => $from->format('Y-m-d'),
            'to_date'              => $to->format('Y-m-d'),
            'term_start'           => $termStart?->format('Y-m-d') ?? '',
            'term_end'             => $termEnd?->format('Y-m-d') ?? '',
            'books'                => array_map(self::bookToArray(...), $ledger->books),
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('ledger/view.html.tpl', $data));
    }

    /**
     * @return array{id: string, code: string, name: string}
     */
    private static function accountTitleToArray(AccountTitle $a): array
    {
        return ['id' => $a->id, 'code' => $a->code, 'name' => $a->name];
    }

    /**
     * @return array{
     *   accountTitleId: string,
     *   accountTitleCode: string,
     *   accountTitleName: string,
     *   openingBalance: string,
     *   debitTotal: string,
     *   creditTotal: string,
     *   closingBalance: string,
     *   entries: list<array<string, string>>,
     * }
     */
    private static function bookToArray(LedgerBook $b): array
    {
        return [
            'accountTitleId'   => $b->accountTitleId,
            'accountTitleCode' => $b->accountTitleCode,
            'accountTitleName' => $b->accountTitleName,
            'openingBalance'   => self::formatAmount($b->openingBalance),
            'debitTotal'       => self::formatAmount($b->debitTotal),
            'creditTotal'      => self::formatAmount($b->creditTotal),
            'closingBalance'   => self::formatAmount($b->closingBalance),
            'entries'          => array_map(self::entryToArray(...), $b->entries),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function entryToArray(LedgerEntry $e): array
    {
        return [
            'entryDate'          => $e->entryDate->format('Y-m-d'),
            'counterAccountCode' => $e->counterAccountCode,
            'counterAccountName' => $e->counterAccountName,
            'summary'            => $e->summary,
            'memo'               => $e->memo,
            'debitAmount'        => self::formatAmount($e->debitAmount),
            'creditAmount'       => self::formatAmount($e->creditAmount),
            'runningBalance'     => self::formatAmount($e->runningBalance),
        ];
    }

    /**
     * JPY-only thousands format with parentheses for negatives; mirrors the
     * presentation rules used by the dompdf ledger generator.
     */
    private static function formatAmount(string $raw): string
    {
        if ($raw === '' || !is_numeric($raw)) {
            return '0';
        }
        $num = (float) $raw;
        if ($num === 0.0) {
            return '0';
        }
        $formatted = number_format(abs($num), 0, '.', ',');
        return $num < 0 ? '(' . $formatted . ')' : $formatted;
    }
}
