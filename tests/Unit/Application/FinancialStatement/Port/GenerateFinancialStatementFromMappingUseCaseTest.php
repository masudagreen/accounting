<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\FinancialStatement\Port\GenerateFinancialStatementFromMappingUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\FinancialStatement\InMemoryAccountTitleRepository;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

#[CoversClass(GenerateFinancialStatementFromMappingUseCase::class)]
#[CoversClass(GenerateFinancialStatementUseCase::class)]
final class GenerateFinancialStatementFromMappingUseCaseTest extends TestCase
{
    private const ENT = 'ENT';
    private const TERM = 'TRM';

    public function testMappingDrivenPathComputesStepwisePl(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();

        // Sales 100000 / Cost 60000 / SGA 20000 / Non-op income 1000 / Non-op expense 500 / Tax 6000
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S',  '401', '売上', 'revenue', 'credit', 'credit', '100000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'C',  '501', '仕入', 'expense', 'debit',  'debit',  '60000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'G',  '502', '販管費', 'expense', 'debit', 'debit', '20000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'NR', '411', '受取利息', 'revenue', 'credit', 'credit', '1000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'NE', '511', '支払利息', 'expense', 'debit',  'debit',  '500');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'TX', '591', '法人税等', 'expense', 'debit',  'debit',  '6000');

        $mappings->seed(self::ENT, 'S',  FsKind::ProfitAndLoss, 'operating_revenue',     1, 10);
        $mappings->seed(self::ENT, 'C',  FsKind::ProfitAndLoss, 'cost_of_sales',         1, 10);
        $mappings->seed(self::ENT, 'G',  FsKind::ProfitAndLoss, 'sga',                   1, 10);
        $mappings->seed(self::ENT, 'NR', FsKind::ProfitAndLoss, 'non_operating_revenue', 1, 10);
        $mappings->seed(self::ENT, 'NE', FsKind::ProfitAndLoss, 'non_operating_expense', 1, 10);
        $mappings->seed(self::ENT, 'TX', FsKind::ProfitAndLoss, 'income_tax',            1, 10);
        // Also mark sales on BS equity retained earnings — we skip BS for this test.

        $trialBalance = new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock());
        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: $trialBalance,
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
        );

        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::ProfitAndLoss,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame('100000.0000', $fs->pl['operating_revenue']->subtotal);
        self::assertSame('60000.0000',  $fs->pl['cost_of_sales']->subtotal);
        self::assertSame('40000.0000',  $fs->pl['gross_profit']->subtotal);
        self::assertSame('20000.0000',  $fs->pl['operating_income']->subtotal);
        self::assertSame('20500.0000',  $fs->pl['ordinary_income']->subtotal);
        self::assertSame('20500.0000',  $fs->pl['pretax_income']->subtotal);
        self::assertSame('14500.0000',  $fs->pl['net_income']->subtotal);

        // Totals aliases stay consistent.
        self::assertSame('14500.0000', $fs->totals['net_income']);
        self::assertSame('40000.0000', $fs->totals['gross_profit']);
        self::assertSame('20500.0000', $fs->totals['ordinary_income']);
    }

    public function testDispatcherFallsBackToSimplifiedWhenNoMappings(): void
    {
        // Entity with no mappings configured should still produce a usable
        // statement via the simplified fallback.
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository(); // empty
        $defs = new InMemoryFsSectionDefinitionRepository();
        $accounts = new InMemoryAccountTitleRepository();

        $accounts->seed(self::ENT, 'ACC_CASH', '101', '現金', 'asset', 'debit');
        $accounts->seed(self::ENT, 'ACC_SALES', '401', '売上', 'revenue', 'credit');

        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '1500');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '1500');

        $dispatcher = new GenerateFinancialStatementUseCase(
            $trialBalance = new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock()),
            $accounts,
            new FrozenClock(),
            $mappings,
            $defs,
            new FinancialStatementBuilder(),
        );

        $fs = $dispatcher->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Simplified path uses the Section::CODE_REVENUE key.
        self::assertArrayHasKey(\Rucaro\Domain\FinancialStatement\Section::CODE_REVENUE, $fs->pl);
        self::assertSame('1500.0000', $fs->totals['net_income']);
    }

    public function testNetIncomeCarriesOverIntoBsEquitySoBsBalances(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();

        // BS: 現金 2_583_000 / 借入金 200_000 / 資本金 1_000_000
        // PL: 売上 2_000_000 / 仕入 617_000 → 当期純利益 1_383_000
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'CASH', '101', '現金',     'asset',     'debit',  'debit',  '2583000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'LOAN', '211', '借入金',   'liability', 'credit', 'credit', '200000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'CAP',  '301', '資本金',   'equity',    'credit', 'credit', '1000000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S',    '401', '売上',     'revenue',   'credit', 'credit', '2000000');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'C',    '501', '仕入',     'expense',   'debit',  'debit',  '617000');

        $mappings->seed(self::ENT, 'CASH', FsKind::BalanceSheet,  'current_asset',     1, 10);
        $mappings->seed(self::ENT, 'LOAN', FsKind::BalanceSheet,  'current_liability', 1, 10);
        $mappings->seed(self::ENT, 'CAP',  FsKind::BalanceSheet,  'capital',           1, 10);
        $mappings->seed(self::ENT, 'S',    FsKind::ProfitAndLoss, 'operating_revenue', 1, 10);
        $mappings->seed(self::ENT, 'C',    FsKind::ProfitAndLoss, 'cost_of_sales',     1, 10);

        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock()),
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
        );

        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame('1383000.0000', $fs->pl['net_income']->subtotal);
        self::assertSame('2583000.0000', $fs->bs['asset_total']->subtotal);
        // Retained earnings picked up the current-period profit line.
        self::assertCount(1, $fs->bs['retained_earnings']->lines);
        self::assertSame('当期純利益', $fs->bs['retained_earnings']->lines[0]->label);
        self::assertSame('1383000.0000', $fs->bs['retained_earnings']->subtotal);
        // Equity chain rebalances so BS is balanced.
        self::assertSame('2383000.0000', $fs->bs['equity']->subtotal);
        self::assertSame('2383000.0000', $fs->bs['equity_total']->subtotal);
        self::assertSame('2583000.0000', $fs->bs['liability_equity_total']->subtotal);
        self::assertSame(
            $fs->bs['asset_total']->subtotal,
            $fs->bs['liability_equity_total']->subtotal,
        );
    }

    public function testDispatcherPrefersPortWhenMappingsExist(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();
        $accounts = new InMemoryAccountTitleRepository();

        $accounts->seed(self::ENT, 'S', '401', '売上', 'revenue', 'credit');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S', '401', '売上', 'revenue', 'credit', 'credit', '5000');
        $mappings->seed(self::ENT, 'S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10);

        $dispatcher = new GenerateFinancialStatementUseCase(
            new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock()),
            $accounts,
            new FrozenClock(),
            $mappings,
            $defs,
            new FinancialStatementBuilder(),
        );

        $fs = $dispatcher->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::ProfitAndLoss,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Port path uses granular code 'operating_revenue' not 'revenue'.
        self::assertArrayHasKey('operating_revenue', $fs->pl);
        self::assertSame('5000.0000', $fs->pl['operating_revenue']->subtotal);
        self::assertSame('5000.0000', $fs->pl['net_income']->subtotal);
    }
}
