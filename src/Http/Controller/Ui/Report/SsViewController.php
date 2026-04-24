<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use InvalidArgumentException;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityInput;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityUseCase;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquityPdfGeneratorInterface;
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
 * GET /ui/ss — 株主資本等変動計算書 (Statement of Changes in Equity, "SS").
 *
 * Flattens the aggregate into a row-per-change-type × column-per-section
 * grid the Smarty template renders verbatim.
 */
final readonly class SsViewController
{
    public function __construct(
        private GenerateStatementOfChangesInEquityUseCase $useCase,
        private StatementOfChangesInEquityPdfGeneratorInterface $pdfGenerator,
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

        $ss = null;
        $errorMessage = '';
        try {
            $ss = $this->useCase->execute(new GenerateStatementOfChangesInEquityInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                fromDate: $from,
                toDate: $to,
            ));
        } catch (InvalidArgumentException $e) {
            $errorMessage = $e->getMessage();
        }

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf' && $ss !== null) {
            $pdf = $this->pdfGenerator->render($ss);
            $filename = sprintf('ss-%s.pdf', $to->format('Ymd'));
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
            'page_title'           => '株主資本等変動計算書',
            'active_nav'           => 'ss',
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
            'has_ss'               => $ss !== null,
            'error_message'        => $errorMessage,
            'ss'                   => $ss !== null ? self::ssToArray($ss) : null,
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('ss/view.html.tpl', $data));
    }

    /**
     * @return array{columns: list<array{code: string, label: string}>, opening: array<string, string>, ending: array<string, string>, rows: list<array{changeType: string, label: string, amounts: array<string, string>}>, totals: array{opening: string, totalChange: string, ending: string}}
     */
    private static function ssToArray(StatementOfChangesInEquity $ss): array
    {
        $columns = [];
        foreach (SsSectionCode::ordered() as $code) {
            $section = $ss->sectionByCode($code);
            if ($section === null) {
                continue;
            }
            $columns[] = ['code' => $code->value, 'label' => $section->label];
        }

        /** @var array<string, string> $opening */
        $opening = [];
        /** @var array<string, string> $ending */
        $ending  = [];
        foreach ($ss->sections as $section) {
            $opening[$section->sectionCode->value] = ViewModelBuilder::formatAmount($section->openingBalance);
            $ending[$section->sectionCode->value]  = ViewModelBuilder::formatAmount($section->endingBalance);
        }

        $rows = [];
        foreach ($ss->changeTypesInUse() as $type) {
            /** @var array<string, string> $amounts */
            $amounts = [];
            foreach ($ss->sections as $section) {
                $sum = '0.0000';
                foreach ($section->changes as $change) {
                    if ($change->changeType === $type) {
                        $sum = \Rucaro\Support\Decimal\Decimal::add($sum, $change->amount);
                    }
                }
                $amounts[$section->sectionCode->value] = ViewModelBuilder::formatAmount($sum);
            }
            $rows[] = [
                'changeType' => $type->value,
                'label'      => $type->label(),
                'amounts'    => $amounts,
            ];
        }

        $totals = $ss->totals();
        return [
            'columns' => $columns,
            'opening' => $opening,
            'ending'  => $ending,
            'rows'    => $rows,
            'totals'  => [
                'opening'     => ViewModelBuilder::formatAmount($totals['opening']),
                'totalChange' => ViewModelBuilder::formatAmount($totals['totalChange']),
                'ending'      => ViewModelBuilder::formatAmount($totals['ending']),
            ],
        ];
    }
}
