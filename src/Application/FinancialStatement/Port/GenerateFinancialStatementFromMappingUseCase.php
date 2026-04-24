<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Port;

use DateTimeZone;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionCode;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\Service\CashFlowStatementBuilder;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\FsSectionCode;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Port of the legacy Jpn_FinancialStatement calculation pipeline onto the
 * layered architecture.
 *
 * Unlike the transitional simplified use case (which infers BS/PL sections
 * from {@see \Rucaro\Domain\AccountTitle\AccountTitle::category}), this port
 * consumes the explicit `account_title_fs_mappings` + `fs_section_definitions`
 * master — the same model the legacy `accountingFSJpn` JSON columns carried,
 * but normalised into proper tables.
 *
 * Output is still a {@see FinancialStatement} aggregate keyed by the same
 * section codes as before (assets / liabilities / equity / revenue / expenses)
 * so existing JSON serializers and HTTP consumers keep working without
 * changes. Under the hood, the section map also carries every granular
 * J-GAAP section (gross_profit, operating_income, ordinary_income, …) so
 * renderers or API clients that want the full hierarchy can read them.
 *
 * Wave 6-B extends this port with the Cash Flow Statement (indirect method)
 * via {@see CashFlowStatementBuilder}. The CS dependencies (`csMappings`,
 * `csDefinitions`, `csBuilder`) are optional — when not injected (pre-Wave 6-B
 * callers, unit tests that only exercise BS/PL), CS falls back to `[]` so
 * existing integration tests remain valid.
 */
final readonly class GenerateFinancialStatementFromMappingUseCase
{
    public function __construct(
        private QueryTrialBalanceUseCase $trialBalance,
        private AccountTitleFsMappingRepositoryInterface $mappings,
        private FsSectionDefinitionRepositoryInterface $definitions,
        private FinancialStatementBuilder $builder,
        private ClockInterface $clock = new SystemClock(),
        private ?AccountTitleCsMappingRepositoryInterface $csMappings = null,
        private ?CsSectionDefinitionRepositoryInterface $csDefinitions = null,
        private ?CashFlowStatementBuilder $csBuilder = null,
    ) {
    }

    public function execute(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));

        $tb = $this->trialBalance->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fiscalTermStartDate: $input->fromDate,
            asOf: $input->asOf,
            currencyCode: $input->currencyCode,
        ));

        $mappings = $this->mappings->findAllByEntity($input->entityId);
        $bsDefs = $this->definitions->findAllByKind(FsKind::BalanceSheet);
        $plDefs = $this->definitions->findAllByKind(FsKind::ProfitAndLoss);

        $bs = $input->kind->includesBalanceSheet()
            ? $this->builder->build(FsKind::BalanceSheet, $tb->rows, $mappings, $bsDefs)
            : [];

        // PL is computed whenever the caller wants PL or CS (CS needs the
        // pretax-income subtotal) — we discard it later if the caller only
        // wanted CS on output.
        $wantsCs = $input->kind->includesCashFlow() && $this->hasCsDependencies();
        $plNeeded = $input->kind->includesProfitAndLoss() || $wantsCs;
        $pl = $plNeeded
            ? $this->builder->build(FsKind::ProfitAndLoss, $tb->rows, $mappings, $plDefs)
            : [];

        // When both BS and PL were requested, fold the current-period net
        // income into retained earnings on the BS so `asset_total` balances
        // `liability_total + equity_total`. Legacy behaviour (see
        // Batch14311::_loopBatchVarsValue) carried period-end net income
        // forward explicitly; here we inline the same effect at read time.
        $bs = $this->applyNetIncomeCarryOver($bs, $pl);

        // Wave 6-B: build the indirect-method Cash Flow Statement when the CS
        // dependencies are wired AND the caller asked for CS (or ALL).
        $cs = [];
        if ($wantsCs) {
            $pretaxIncome = $pl[FsSectionCode::PL_PRETAX_INCOME]->subtotal ?? '0.0000';
            // Beginning cash is not yet sourced from a prior-period snapshot —
            // callers who want that should seed it via the application layer
            // once the prior-period port lands. For now we default to 0 so CS
            // still reconciles to `ending_cash = operating + investing + financing`.
            $beginningCash = '0.0000';
            $cs = $this->buildCashFlow($input->entityId, $tb->rows, $pretaxIncome, $beginningCash);
        }

        // If the caller only asked for CS we internally built the PL to pull
        // pretax income — drop it from the output so the contract matches
        // `kind=CS only`.
        if (!$input->kind->includesProfitAndLoss()) {
            $pl = [];
        }

        $totals = self::buildTotals($bs, $pl, $cs);

        return new FinancialStatement(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            kind: $input->kind,
            fromDate: $input->fromDate,
            toDate: $input->asOf,
            currencyCode: $input->currencyCode,
            bs: $bs,
            pl: $pl,
            cs: $cs,
            totals: $totals,
            generatedAt: $generatedAt,
        );
    }

    /**
     * Expose a small set of commonly-referenced J-GAAP subtotals under
     * the flat `totals` array — matches the existing OpenAPI contract.
     *
     * @param array<string, Section> $bs
     * @param array<string, Section> $pl
     * @param array<string, Section> $cs
     * @return array<string, string>
     */
    private static function buildTotals(array $bs, array $pl, array $cs = []): array
    {
        $totals = [
            'net_income'        => $pl[FsSectionCode::PL_NET_INCOME]->subtotal ?? '0.0000',
            'total_assets'      => $bs[FsSectionCode::BS_ASSET]->subtotal ?? '0.0000',
            'total_liabilities' => $bs[FsSectionCode::BS_LIABILITY]->subtotal ?? '0.0000',
            'total_equity'      => $bs[FsSectionCode::BS_EQUITY]->subtotal ?? '0.0000',
            'total_revenue'     => $pl[FsSectionCode::PL_OPERATING_REVENUE]->subtotal ?? '0.0000',
            'total_expenses'    => self::sumOf(
                $pl[FsSectionCode::PL_COST_OF_SALES]->subtotal ?? '0.0000',
                $pl[FsSectionCode::PL_SGA]->subtotal ?? '0.0000',
            ),
            'gross_profit'      => $pl[FsSectionCode::PL_GROSS_PROFIT]->subtotal ?? '0.0000',
            'operating_income'  => $pl[FsSectionCode::PL_OPERATING_INCOME]->subtotal ?? '0.0000',
            'ordinary_income'   => $pl[FsSectionCode::PL_ORDINARY_INCOME]->subtotal ?? '0.0000',
            'pretax_income'     => $pl[FsSectionCode::PL_PRETAX_INCOME]->subtotal ?? '0.0000',
        ];

        if ($cs !== []) {
            $totals['operating_cf_total'] = $cs[CsSectionCode::OPERATING_CF_TOTAL]->subtotal ?? '0.0000';
            $totals['investing_cf_total'] = $cs[CsSectionCode::INVESTING_CF_TOTAL]->subtotal ?? '0.0000';
            $totals['financing_cf_total'] = $cs[CsSectionCode::FINANCING_CF_TOTAL]->subtotal ?? '0.0000';
            $totals['net_change_in_cash'] = $cs[CsSectionCode::NET_CHANGE_IN_CASH]->subtotal ?? '0.0000';
            $totals['ending_cash']        = $cs[CsSectionCode::ENDING_CASH]->subtotal ?? '0.0000';
        }

        return $totals;
    }

    private function hasCsDependencies(): bool
    {
        return $this->csMappings !== null
            && $this->csDefinitions !== null
            && $this->csBuilder !== null;
    }

    /**
     * @param list<\Rucaro\Domain\TrialBalance\TrialBalanceRow> $rows
     * @return array<string, Section>
     */
    private function buildCashFlow(
        string $entityId,
        array $rows,
        string $pretaxIncome,
        string $beginningCash,
    ): array {
        if (!$this->hasCsDependencies()) {
            return [];
        }
        /** @var AccountTitleCsMappingRepositoryInterface $mappingsRepo */
        $mappingsRepo = $this->csMappings;
        /** @var CsSectionDefinitionRepositoryInterface $defsRepo */
        $defsRepo = $this->csDefinitions;
        /** @var CashFlowStatementBuilder $builder */
        $builder = $this->csBuilder;

        $csMappings = $mappingsRepo->findAllByEntity($entityId);
        $csDefs = $defsRepo->findAll();
        if ($csDefs === []) {
            return [];
        }

        return $builder->build(
            periodRows: $rows,
            priorRows: [],
            mappings: $csMappings,
            definitions: $csDefs,
            pretaxIncome: $pretaxIncome,
            beginningCash: $beginningCash,
        );
    }

    private static function sumOf(string $a, string $b): string
    {
        return \Rucaro\Support\Decimal\Decimal::add($a, $b);
    }

    /**
     * Inject the current-period net income into BS retained earnings and
     * assert the resulting BS balances. Extracted into its own method so
     * Wave 6-B (CS) can add its own closely-scoped pipeline stage without
     * colliding on a fat `execute()` body.
     *
     * The balance assertion only runs when both BS and PL were produced —
     * i.e. the caller asked for `All` (or both sides in a future ALL-ish
     * kind). BS-only and PL-only requests skip the check so half-statements
     * stay renderable, matching the legacy Jpn_FinancialStatement behaviour
     * of only asserting period-end balance after the carry-over integration.
     *
     * @param array<string, Section> $bs
     * @param array<string, Section> $pl
     * @return array<string, Section>
     */
    private function applyNetIncomeCarryOver(array $bs, array $pl): array
    {
        $next = $this->builder->applyNetIncomeCarryOver($bs, $pl);
        if ($bs !== [] && $pl !== []) {
            $this->builder->assertBalanced($next);
        }
        return $next;
    }
}
