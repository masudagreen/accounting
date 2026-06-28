<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FiscalTermLookup;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/trial-balance — renders the 合計残高試算表 (Trial Balance) for the
 * currently selected entity + fiscal term.
 *
 * Mirrors the data path of {@see \Rucaro\Http\Controller\TrialBalance\GetTrialBalanceController}
 * (the REST API surface) — both delegate to {@see QueryTrialBalanceUseCase},
 * which folds in 期首繰越 for BS rows (B-3b). Only the rendering is different:
 * here we hand off to a Smarty template instead of JSON / CSV.
 *
 * Query params:
 *   - year, month  (optional) — see {@see PeriodQueryHelper} for the
 *                               resolution rules.
 *
 * `format=pdf` is intentionally out of scope for B-5 — the API already exposes
 * CSV download and a PDF generator can be wired later (tracked separately).
 */
final readonly class TrialBalanceViewController
{
    public function __construct(
        private QueryTrialBalanceUseCase $useCase,
        private PeriodQueryHelper $period,
        private FiscalTermLookup $fiscalTerms,
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

        $year = PeriodQueryHelper::parseYear($request->queryString('year'));
        $month = PeriodQueryHelper::parseMonth($request->queryString('month'));
        [$from, $to, $termStart, $termEnd] = $this->period->resolve($fiscalTermId, $year, $month);

        $tb = $this->useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fiscalTermStartDate: $from,
            asOf: $to,
        ));

        $data = [
            'page_title' => '合計残高試算表',
            'active_nav' => 'trial_balance',
            'csrf_logout_token' => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token' => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field' => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field' => EntitySwitchController::CSRF_FORM_ID,
            'display_name' => $this->session->getDisplayName() ?? '',
            'user_email' => $this->session->getEmail() ?? '',
            'selected_entity_id' => $entityId,
            'selected_fiscal_term' => $fiscalTermId,
            'selected_fiscal_term_id' => $fiscalTermId,
            'entities' => [],
            // F-1: feed the navbar's fiscal-term <select>.
            'nav_fiscal_terms' => $this->fiscalTerms->listForEntity($entityId),
            'year' => $year !== null ? (string) $year : '',
            'month' => $month !== null ? (string) $month : '',
            'from_date' => $from->format('Y-m-d'),
            'to_date' => $to->format('Y-m-d'),
            'term_start' => $termStart?->format('Y-m-d') ?? '',
            'term_end' => $termEnd?->format('Y-m-d') ?? '',
            'rows' => array_map(self::rowToArray(...), $tb->rows),
            'totals' => [
                'debit' => self::formatAmount($tb->debitTotal()),
                'credit' => self::formatAmount($tb->creditTotal()),
                'balanced' => $tb->isBalanced(),
            ],
            'flash_messages' => $this->flash->consume(),
        ];

        return HtmlResponse::ok($this->view->render('trial_balance/view.html.tpl', $data));
    }

    /**
     * @return array{
     *   accountTitleId: string,
     *   accountTitleCode: string,
     *   accountTitleName: string,
     *   accountCategory: string,
     *   accountCategoryLabel: string,
     *   normalSide: string,
     *   debitTotal: string,
     *   creditTotal: string,
     *   openingBalance: string,
     *   balance: string,
     *   lineCount: int,
     * }
     */
    private static function rowToArray(TrialBalanceRow $r): array
    {
        return [
            'accountTitleId' => $r->accountTitleId,
            'accountTitleCode' => $r->accountTitleCode,
            'accountTitleName' => $r->accountTitleName,
            'accountCategory' => $r->accountCategory,
            'accountCategoryLabel' => self::categoryLabel($r->accountCategory),
            'normalSide' => $r->normalSide,
            'debitTotal' => self::formatAmount($r->debitTotal),
            'creditTotal' => self::formatAmount($r->creditTotal),
            'openingBalance' => self::formatAmount($r->openingBalance),
            'balance' => self::formatAmount($r->balance),
            'lineCount' => $r->lineCount,
        ];
    }

    /**
     * Map the canonical category strings used throughout the domain
     * (asset / liability / equity / revenue / expense / other) to their
     * Japanese human labels for the table column.
     */
    private static function categoryLabel(string $category): string
    {
        return match ($category) {
            'asset' => '資産',
            'liability' => '負債',
            'equity' => '純資産',
            'revenue' => '収益',
            'expense' => '費用',
            default => $category === '' ? '—' : $category,
        };
    }

    /**
     * JPY-only thousands format with parentheses for negatives. Mirrors
     * {@see LedgerViewController::formatAmount()} so all reports format the
     * same way.
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

        return $num < 0 ? '('.$formatted.')' : $formatted;
    }
}
