<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementGeneratorInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
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
 * GET /ui/bs — renders the traditional T-form Balance Sheet (貸借対照表)
 * for the currently selected entity + fiscal term.
 *
 * Reuses the same J-GAAP section ordering as the PDF renderer so the HTML
 * and PDF views stay in lock-step without duplicating the group map.
 */
final readonly class BsViewController
{
    public function __construct(
        private GenerateFinancialStatementUseCase $useCase,
        private FinancialStatementGeneratorInterface $pdfGenerator,
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

        $fs = $this->useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: $from,
            asOf: $to,
        ));

        $format = strtolower($request->queryString('format') ?? 'html');
        if ($format === 'pdf') {
            $pdf = $this->pdfGenerator->render($fs);
            $filename = sprintf('bs-%s.pdf', $to->format('Ymd'));
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
            'page_title'           => '貸借対照表',
            'active_nav'           => 'bs',
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
            'bs'                   => ViewModelBuilder::sectionMap($fs->bs),
            'bs_order'             => ViewModelBuilder::bsOrder(),
            'has_jgaap'            => self::hasJgaap($fs),
            'totals'               => ViewModelBuilder::formatTotals($fs->totals),
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('bs/view.html.tpl', $data));
    }

    private static function hasJgaap(FinancialStatement $fs): bool
    {
        return isset($fs->bs['current_asset']);
    }
}
