<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use InvalidArgumentException;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementInput;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodEntry;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Http\Controller\Ui\EntitySwitchController;
use Rucaro\Http\Controller\Ui\LogoutController;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodFinancialStatementGeneratorInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;

/**
 * GET /ui/fs/multi — read-only multi-period (up to 5 terms) comparison
 * financial statements (複数期比較決算書). The Web UI mirrors the /api/v1
 * variant; same usecase, same PDF generator.
 */
final readonly class MultiPeriodFsViewController
{
    public function __construct(
        private GenerateMultiPeriodFinancialStatementUseCase $useCase,
        private MultiPeriodFinancialStatementGeneratorInterface $pdfGenerator,
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

        $termsRaw = $request->queryString('termIds') ?? '';
        $termIds = self::parseIdList($termsRaw);

        // Default: just the currently selected fiscal term, so the page always
        // renders something on a fresh visit.
        if ($termIds === []) {
            $selected = $this->session->getSelectedFiscalTerm()
                ?? $this->period->findLatestFiscalTermId($entityId);
            if ($selected !== null) {
                $termIds = [$selected];
            }
        }

        $kind = FinancialStatementKind::fromQueryString($request->queryString('kind'));
        $format = strtolower($request->queryString('format') ?? 'html');

        $multi = null;
        $errorMessage = '';
        if ($termIds !== []) {
            try {
                $multi = $this->useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
                    entityId: $entityId,
                    fiscalTermIds: $termIds,
                    kind: $kind,
                ));
            } catch (InvalidArgumentException $e) {
                $errorMessage = $e->getMessage();
            }
        } else {
            $errorMessage = '比較対象の会計期 (termIds) が指定されていません。';
        }

        if ($format === 'pdf' && $multi !== null) {
            $pdf = $this->pdfGenerator->render($multi);
            $filename = sprintf(
                'multi-period-%s-%s.pdf',
                strtolower($kind->value),
                date('Ymd'),
            );
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
            'page_title'           => '複数期比較決算書',
            'active_nav'           => 'fs_multi',
            'csrf_logout_token'    => $this->csrf->generateToken(LogoutController::CSRF_FORM_ID),
            'csrf_entity_token'    => $this->csrf->generateToken(EntitySwitchController::CSRF_FORM_ID),
            'csrf_logout_field'    => LogoutController::CSRF_FORM_ID,
            'csrf_entity_field'    => EntitySwitchController::CSRF_FORM_ID,
            'display_name'         => $this->session->getDisplayName() ?? '',
            'user_email'           => $this->session->getEmail() ?? '',
            'selected_entity_id'   => $entityId,
            'selected_fiscal_term' => $this->session->getSelectedFiscalTerm() ?? '',
            'entities'             => [],
            'term_ids_csv'         => implode(',', $termIds),
            'kind'                 => $kind->value,
            'error_message'        => $errorMessage,
            'has_multi'            => $multi !== null,
            'periods'              => $multi !== null ? self::periodsToArray($multi) : [],
            'kind_is_all'          => $kind === FinancialStatementKind::All,
            'kind_is_bs'           => $kind === FinancialStatementKind::BalanceSheet || $kind === FinancialStatementKind::All,
            'kind_is_pl'           => $kind === FinancialStatementKind::ProfitAndLoss || $kind === FinancialStatementKind::All,
            'kind_is_cs'           => $kind === FinancialStatementKind::CashFlow || $kind === FinancialStatementKind::All,
            'flash_messages'       => $this->flash->consume(),
        ];
        return HtmlResponse::ok($this->view->render('fs_multi/view.html.tpl', $data));
    }

    /**
     * @return list<array{fiscalTermId: string, label: string, fromDate: string, toDate: string, bs: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, pl: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, cs: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, totals: array<string, string>}>
     */
    private static function periodsToArray(MultiPeriodFinancialStatement $multi): array
    {
        $out = [];
        foreach ($multi->periods as $entry) {
            $out[] = self::entryToArray($entry);
        }
        return $out;
    }

    /**
     * @return array{fiscalTermId: string, label: string, fromDate: string, toDate: string, bs: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, pl: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, cs: array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>, totals: array<string, string>}
     */
    private static function entryToArray(MultiPeriodEntry $entry): array
    {
        return [
            'fiscalTermId' => $entry->fiscalTermId,
            'label'        => $entry->fiscalTermLabel,
            'fromDate'     => $entry->fromDate->format('Y-m-d'),
            'toDate'       => $entry->toDate->format('Y-m-d'),
            'bs'           => ViewModelBuilder::sectionMap($entry->statement->bs),
            'pl'           => ViewModelBuilder::sectionMap($entry->statement->pl),
            'cs'           => ViewModelBuilder::sectionMap($entry->statement->cs),
            'totals'       => ViewModelBuilder::formatTotals($entry->statement->totals),
        ];
    }

    /**
     * @return list<string>
     */
    private static function parseIdList(string $raw): array
    {
        if ($raw === '') {
            return [];
        }
        $parts = array_map('trim', explode(',', $raw));
        /** @var list<string> $out */
        $out = [];
        foreach ($parts as $p) {
            if ($p === '' || !UlidGenerator::isValid($p)) {
                continue;
            }
            $out[] = $p;
        }
        return $out;
    }
}
