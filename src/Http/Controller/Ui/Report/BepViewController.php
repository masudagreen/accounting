<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use InvalidArgumentException;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointInput;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointUseCase;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
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
 * GET /ui/bep — Break-Even Point (損益分岐点分析) view.
 *
 * Numbers come from {@see AnalyzeBreakEvenPointUseCase} and the PDF path
 * re-uses the existing {@see BreakEvenPointPdfGeneratorInterface} so the
 * HTML preview and the print output never drift.
 */
final readonly class BepViewController
{
    public function __construct(
        private AnalyzeBreakEvenPointUseCase $useCase,
        private BreakEvenPointPdfGeneratorInterface $pdfGenerator,
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

        $analysis = null;
        $errorMessage = '';
        try {
            $analysis = $this->useCase->execute(new AnalyzeBreakEvenPointInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                fromDate: $from,
                toDate: $to,
            ));
        } catch (InvalidArgumentException $e) {
            $errorMessage = $e->getMessage();
        }

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf' && $analysis !== null) {
            $pdf = $this->pdfGenerator->render($analysis);
            $filename = sprintf('bep-%s.pdf', $to->format('Ymd'));
            return new HtmlResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        $data = [
            'page_title'           => '損益分岐点分析',
            'active_nav'           => 'bep',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $fiscalTermId,
            'entities'             => [],
            'year'                 => $year !== null ? (string) $year : '',
            'month'                => $month !== null ? (string) $month : '',
            'from_date'            => $from->format('Y-m-d'),
            'to_date'              => $to->format('Y-m-d'),
            'term_start'           => $termStart?->format('Y-m-d') ?? '',
            'term_end'             => $termEnd?->format('Y-m-d') ?? '',
            'has_analysis'         => $analysis !== null,
            'error_message'        => $errorMessage,
            'bep'                  => $analysis !== null ? self::analysisToArray($analysis) : null,
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('bep/view.html.tpl', $data));
    }

    /**
     * @return array{sales: string, variableCosts: string, fixedCosts: string, contributionMargin: string, contributionMarginRate: string, bepSales: string, bepRatio: string, safetyMarginRatio: string, operatingProfit: string, belowBep: bool, salesBreakdown: list<array{code: string, name: string, amount: string}>, variableBreakdown: list<array{code: string, name: string, costType: string, amount: string}>, fixedBreakdown: list<array{code: string, name: string, costType: string, amount: string}>}
     */
    private static function analysisToArray(BreakEvenPointAnalysis $a): array
    {
        return [
            'sales'                  => ViewModelBuilder::formatAmount($a->sales),
            'variableCosts'          => ViewModelBuilder::formatAmount($a->variableCosts),
            'fixedCosts'             => ViewModelBuilder::formatAmount($a->fixedCosts),
            'contributionMargin'     => ViewModelBuilder::formatAmount($a->contributionMargin),
            'contributionMarginRate' => self::formatPercent($a->contributionMarginRate),
            'bepSales'               => ViewModelBuilder::formatAmount($a->bepSales),
            'bepRatio'               => self::formatPercent($a->bepRatio),
            'safetyMarginRatio'      => self::formatPercent($a->safetyMarginRatio),
            'operatingProfit'        => ViewModelBuilder::formatAmount($a->operatingProfit),
            'belowBep'               => $a->isBelowBreakEven(),
            'salesBreakdown'         => array_map(
                static fn (array $r): array => [
                    'code'   => $r['accountTitleCode'],
                    'name'   => $r['accountTitleName'],
                    'amount' => ViewModelBuilder::formatAmount($r['amount']),
                ],
                $a->salesBreakdown,
            ),
            'variableBreakdown'      => array_map(
                static fn (array $r): array => [
                    'code'     => $r['accountTitleCode'],
                    'name'     => $r['accountTitleName'],
                    'costType' => $r['costType'],
                    'amount'   => ViewModelBuilder::formatAmount($r['amount']),
                ],
                $a->variableBreakdown,
            ),
            'fixedBreakdown'         => array_map(
                static fn (array $r): array => [
                    'code'     => $r['accountTitleCode'],
                    'name'     => $r['accountTitleName'],
                    'costType' => $r['costType'],
                    'amount'   => ViewModelBuilder::formatAmount($r['amount']),
                ],
                $a->fixedBreakdown,
            ),
        ];
    }

    /**
     * Converts a scale-4 decimal fraction (e.g. "0.8750") to a percentage
     * string with one decimal place ("87.5%").
     */
    private static function formatPercent(string $raw): string
    {
        if (!is_numeric($raw)) {
            return '0.0%';
        }
        $pct = ((float) $raw) * 100.0;
        return sprintf('%.1f%%', $pct);
    }
}
