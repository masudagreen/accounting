<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port\Cs;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\FinancialStatement\Port\GenerateFinancialStatementFromMappingUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsFlowCategory;
use Rucaro\Domain\FinancialStatement\Port\Cs\Service\CashFlowStatementBuilder;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\InMemoryAccountTitleFsMappingRepository;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\InMemoryFsSectionDefinitionRepository;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

#[CoversClass(GenerateFinancialStatementFromMappingUseCase::class)]
final class GenerateFinancialStatementFromMappingUseCaseCsTest extends TestCase
{
    private const ENT = 'ENT';
    private const TERM = 'TRM';

    public function testKindCsBuildsIndirectMethodCashFlowStatement(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();
        $csMappings = new InMemoryAccountTitleCsMappingRepository();
        $csDefs = new InMemoryCsSectionDefinitionRepository();

        // Build a PL that yields 1000 pretax income (revenue 100000 - cost 60000 - sga 20000 + non-op 0 + extra 0 = …)
        // Keep it compact: revenue 1200, cost 200, = GP 1000, = OI 1000, = Ord 1000, = Pretax 1000.
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S',   '401', '売上',       'revenue', 'credit', 'credit', '1200');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'C',   '501', '仕入',       'expense', 'debit',  'debit',  '200');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'DEP', '531', '減価償却費', 'expense', 'debit',  'debit',  '300');

        $mappings->seed(self::ENT, 'S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10);
        $mappings->seed(self::ENT, 'C', FsKind::ProfitAndLoss, 'cost_of_sales',     1, 10);
        $mappings->seed(self::ENT, 'DEP', FsKind::ProfitAndLoss, 'sga',             1, 10);

        // CS: 減価償却費を営業 CF に加算（非資金項目）
        $csMappings->seed(self::ENT, 'DEP', 'depreciation', CsFlowCategory::Operating, 1, false, 10);

        $trialBalance = new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock());
        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: $trialBalance,
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
            csMappings: $csMappings,
            csDefinitions: $csDefs,
            csBuilder: new CashFlowStatementBuilder(),
        );

        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::CashFlow,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Pretax income from PL: 1200 - 200 - 300 = 700.
        self::assertSame('700.0000', $fs->cs['operating_pretax_income']->subtotal);
        self::assertSame('300.0000', $fs->cs['depreciation']->subtotal);
        // Operating CF = 700 + 300 = 1000.
        self::assertSame('1000.0000', $fs->cs['operating_cf_total']->subtotal);
        // Ending cash = net change (operating only) = 1000
        self::assertSame('1000.0000', $fs->cs['net_change_in_cash']->subtotal);
        self::assertSame('1000.0000', $fs->cs['ending_cash']->subtotal);

        // Totals aliases cover CS when present.
        self::assertSame('1000.0000', $fs->totals['operating_cf_total']);
        self::assertSame('0.0000',    $fs->totals['investing_cf_total']);
        self::assertSame('0.0000',    $fs->totals['financing_cf_total']);
        self::assertSame('1000.0000', $fs->totals['ending_cash']);
    }

    public function testKindAllIncludesBsPlCsTogether(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();
        $csMappings = new InMemoryAccountTitleCsMappingRepository();
        $csDefs = new InMemoryCsSectionDefinitionRepository();

        // Minimal: sale 500 vs cost 300 → GP=200, OI=200, pretax=200. Equity 200 retained.
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'CASH', '101', '現金',     'asset',     'debit',  'debit',  '200');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'CAP',  '301', '資本金',   'equity',    'credit', 'credit', '0'); // no movement
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S',    '401', '売上',     'revenue',   'credit', 'credit', '500');
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'C',    '501', '仕入',     'expense',   'debit',  'debit',  '300');

        $mappings->seed(self::ENT, 'CASH', FsKind::BalanceSheet, 'current_asset',     1, 10);
        $mappings->seed(self::ENT, 'S',    FsKind::ProfitAndLoss, 'operating_revenue', 1, 10);
        $mappings->seed(self::ENT, 'C',    FsKind::ProfitAndLoss, 'cost_of_sales',     1, 10);

        $trialBalance = new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock());
        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: $trialBalance,
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
            csMappings: $csMappings,
            csDefinitions: $csDefs,
            csBuilder: new CashFlowStatementBuilder(),
        );

        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertNotEmpty($fs->bs);
        self::assertNotEmpty($fs->pl);
        self::assertNotEmpty($fs->cs);
        self::assertSame('200.0000', $fs->pl['net_income']->subtotal);
        // Operating CF pulled from pretax only (no depreciation mapping here) = 200
        self::assertSame('200.0000', $fs->cs['operating_cf_total']->subtotal);
    }

    public function testKindCsWithoutCsDependenciesProducesEmptyCs(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();
        $mappings->seed(self::ENT, 'S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10);
        $tb->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'S', '401', '売上', 'revenue', 'credit', 'credit', '100');

        $trialBalance = new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock());
        // No CS deps passed → CS stays empty even for kind=CS.
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
            kind: FinancialStatementKind::CashFlow,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame([], $fs->cs);
    }
}
