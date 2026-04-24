<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatement\Port;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMapping;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\InMemoryFsSectionDefinitionRepository;

#[CoversClass(FinancialStatementBuilder::class)]
#[CoversClass(FsSectionDefinition::class)]
final class FinancialStatementBuilderTest extends TestCase
{
    public function testSimplePlComputesStagedProfitsCorrectly(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];

        $rows = [
            self::row('ACC_SALES', '401', '売上', 'revenue', 'credit', '0', '10000'),
            self::row('ACC_COST',  '501', '仕入', 'expense', 'debit',  '4000', '0'),
            self::row('ACC_SGA',   '502', '給料', 'expense', 'debit',  '2000', '0'),
        ];
        $mappings = [
            new AccountTitleFsMapping('ACC_SALES', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10, null),
            new AccountTitleFsMapping('ACC_COST',  FsKind::ProfitAndLoss, 'cost_of_sales',     1, 10, null),
            new AccountTitleFsMapping('ACC_SGA',   FsKind::ProfitAndLoss, 'sga',               1, 10, null),
        ];

        $pl = $builder->build(FsKind::ProfitAndLoss, $rows, $mappings, $defs);

        self::assertSame('10000.0000', $pl['operating_revenue']->subtotal);
        self::assertSame('4000.0000',  $pl['cost_of_sales']->subtotal);
        self::assertSame('6000.0000',  $pl['gross_profit']->subtotal);       // 10000 - 4000
        self::assertSame('2000.0000',  $pl['sga']->subtotal);
        self::assertSame('4000.0000',  $pl['operating_income']->subtotal);   // 6000 - 2000
        self::assertSame('4000.0000',  $pl['ordinary_income']->subtotal);    // + 0 - 0
        self::assertSame('4000.0000',  $pl['pretax_income']->subtotal);      // + 0 - 0
        self::assertSame('4000.0000',  $pl['net_income']->subtotal);
    }

    public function testNonOperatingAndExtraordinaryFlowUpThroughStagedProfits(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];

        $rows = [
            self::row('S', '401', '売上', 'revenue', 'credit', '0', '100000'),
            self::row('C', '501', '仕入', 'expense', 'debit',  '60000', '0'),
            self::row('G', '502', '販管費', 'expense', 'debit', '20000', '0'),
            self::row('NR','411', '受取利息', 'revenue', 'credit', '0', '1000'),
            self::row('NE','511', '支払利息', 'expense', 'debit', '500', '0'),
            self::row('EG','412', '固定資産売却益', 'revenue', 'credit', '0', '5000'),
            self::row('EL','512', '災害損失', 'expense', 'debit', '3000', '0'),
            self::row('TX','591', '法人税等', 'expense', 'debit', '6000', '0'),
        ];
        $mappings = [
            new AccountTitleFsMapping('S',  FsKind::ProfitAndLoss, 'operating_revenue',     1, 10, null),
            new AccountTitleFsMapping('C',  FsKind::ProfitAndLoss, 'cost_of_sales',         1, 10, null),
            new AccountTitleFsMapping('G',  FsKind::ProfitAndLoss, 'sga',                   1, 10, null),
            new AccountTitleFsMapping('NR', FsKind::ProfitAndLoss, 'non_operating_revenue', 1, 10, null),
            new AccountTitleFsMapping('NE', FsKind::ProfitAndLoss, 'non_operating_expense', 1, 10, null),
            new AccountTitleFsMapping('EG', FsKind::ProfitAndLoss, 'extraordinary_gain',    1, 10, null),
            new AccountTitleFsMapping('EL', FsKind::ProfitAndLoss, 'extraordinary_loss',    1, 10, null),
            new AccountTitleFsMapping('TX', FsKind::ProfitAndLoss, 'income_tax',            1, 10, null),
        ];

        $pl = $builder->build(FsKind::ProfitAndLoss, $rows, $mappings, $defs);

        self::assertSame('40000.0000', $pl['gross_profit']->subtotal);      // 100000 - 60000
        self::assertSame('20000.0000', $pl['operating_income']->subtotal);  // 40000 - 20000
        self::assertSame('20500.0000', $pl['ordinary_income']->subtotal);   // 20000 + 1000 - 500
        self::assertSame('22500.0000', $pl['pretax_income']->subtotal);     // 20500 + 5000 - 3000
        self::assertSame('16500.0000', $pl['net_income']->subtotal);        // 22500 - 6000
    }

    public function testSignNegativeSubtractsContraItem(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::BalanceSheet->value];

        // 売掛金 1000, 貸倒引当金 50 (contra asset) → current_asset subtotal = 950.
        $rows = [
            self::row('AR',  '121', '売掛金',      'asset', 'debit', '1000', '0'),
            self::row('ALL', '129', '貸倒引当金',  'asset', 'debit', '50', '0'),
        ];
        $mappings = [
            new AccountTitleFsMapping('AR',  FsKind::BalanceSheet, 'current_asset', 1,  10, null),
            new AccountTitleFsMapping('ALL', FsKind::BalanceSheet, 'current_asset', -1, 20, '貸倒引当金'),
        ];

        $bs = $builder->build(FsKind::BalanceSheet, $rows, $mappings, $defs);

        self::assertSame('950.0000', $bs['current_asset']->subtotal);
        self::assertSame('950.0000', $bs['asset']->subtotal);
        self::assertSame('950.0000', $bs['asset_total']->subtotal);
    }

    public function testChildSectionsRollUpIntoParent(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::BalanceSheet->value];

        $rows = [
            self::row('CASH',  '101', '現金',     'asset', 'debit', '500', '0'),
            self::row('BLDG',  '201', '建物',     'asset', 'debit', '3000', '0'),
            self::row('GOOD',  '211', 'のれん',    'asset', 'debit', '200', '0'),
        ];
        $mappings = [
            new AccountTitleFsMapping('CASH', FsKind::BalanceSheet, 'current_asset',    1, 10, null),
            new AccountTitleFsMapping('BLDG', FsKind::BalanceSheet, 'tangible_asset',   1, 10, null),
            new AccountTitleFsMapping('GOOD', FsKind::BalanceSheet, 'intangible_asset', 1, 10, null),
        ];

        $bs = $builder->build(FsKind::BalanceSheet, $rows, $mappings, $defs);

        self::assertSame('500.0000',  $bs['current_asset']->subtotal);
        self::assertSame('3000.0000', $bs['tangible_asset']->subtotal);
        self::assertSame('200.0000',  $bs['intangible_asset']->subtotal);
        // noncurrent_asset rolls up tangible + intangible + investment (0).
        self::assertSame('3200.0000', $bs['noncurrent_asset']->subtotal);
        // asset rolls up current + noncurrent + deferred (0) → 500 + 3200.
        self::assertSame('3700.0000', $bs['asset']->subtotal);
        self::assertSame('3700.0000', $bs['asset_total']->subtotal);
    }

    public function testUnmappedAccountsAreIgnoredNotAppended(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];

        $rows = [
            self::row('S', '401', '売上', 'revenue', 'credit', '0', '1000'),
            self::row('X', '999', 'noise', 'revenue', 'credit', '0', '9999'), // no mapping
        ];
        $mappings = [
            new AccountTitleFsMapping('S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10, null),
        ];

        $pl = $builder->build(FsKind::ProfitAndLoss, $rows, $mappings, $defs);
        self::assertSame('1000.0000', $pl['operating_revenue']->subtotal);
        self::assertSame('1000.0000', $pl['gross_profit']->subtotal);
        self::assertSame('1000.0000', $pl['net_income']->subtotal);
    }

    public function testEmptyInputsProduceZeroEverywhere(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];
        $pl = $builder->build(FsKind::ProfitAndLoss, [], [], $defs);
        self::assertSame('0.0000', $pl['operating_revenue']->subtotal);
        self::assertSame('0.0000', $pl['net_income']->subtotal);
    }

    public function testFormulaParsing(): void
    {
        $def = new FsSectionDefinition(
            FsKind::ProfitAndLoss,
            'gross_profit',
            null,
            '売上総利益',
            30,
            true,
            false,
            '+operating_revenue-cost_of_sales',
        );
        self::assertSame(
            [[1, 'operating_revenue'], [-1, 'cost_of_sales']],
            $def->parsedFormula(),
        );
    }

    public function testSectionsCarryHierarchyMetadataAfterBuild(): void
    {
        $builder = new FinancialStatementBuilder();
        $defs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::BalanceSheet->value];

        $rows = [self::row('CASH', '101', '現金', 'asset', 'debit', '500', '0')];
        $mappings = [new AccountTitleFsMapping('CASH', FsKind::BalanceSheet, 'current_asset', 1, 10, null)];

        $bs = $builder->build(FsKind::BalanceSheet, $rows, $mappings, $defs);

        self::assertSame('asset', $bs['current_asset']->parentCode);
        self::assertSame(10, $bs['current_asset']->sortOrder);
        self::assertFalse($bs['current_asset']->isSubtotal);
        self::assertFalse($bs['current_asset']->isTotal);
        self::assertNull($bs['asset']->parentCode);
        self::assertTrue($bs['asset_total']->isTotal);
        self::assertSame(99, $bs['asset_total']->sortOrder);
    }

    public function testApplyNetIncomeCarryOverInjectsProfitIntoRetainedEarnings(): void
    {
        $builder = new FinancialStatementBuilder();
        $bsDefs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::BalanceSheet->value];
        $plDefs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];

        // BS: cash 2_583_000 asset, 借入金 200_000 liability, 資本金 1_000_000.
        // After carry-over equity should rise by net income (1_383_000).
        $bsRows = [
            self::row('CASH', '101', '現金',     'asset',     'debit',  '2583000', '0'),
            self::row('LOAN', '211', '借入金',   'liability', 'credit', '0',       '200000'),
            self::row('CAP',  '301', '資本金',   'equity',    'credit', '0',       '1000000'),
        ];
        $bsMaps = [
            new AccountTitleFsMapping('CASH', FsKind::BalanceSheet, 'current_asset',     1, 10, null),
            new AccountTitleFsMapping('LOAN', FsKind::BalanceSheet, 'current_liability', 1, 10, null),
            new AccountTitleFsMapping('CAP',  FsKind::BalanceSheet, 'capital',           1, 10, null),
        ];

        $plRows = [
            self::row('S', '401', '売上', 'revenue', 'credit', '0', '2000000'),
            self::row('C', '501', '仕入', 'expense', 'debit',  '617000', '0'),
        ];
        $plMaps = [
            new AccountTitleFsMapping('S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10, null),
            new AccountTitleFsMapping('C', FsKind::ProfitAndLoss, 'cost_of_sales',     1, 10, null),
        ];

        $bs = $builder->build(FsKind::BalanceSheet, $bsRows, $bsMaps, $bsDefs);
        $pl = $builder->build(FsKind::ProfitAndLoss, $plRows, $plMaps, $plDefs);

        // Before carry-over the BS does not balance.
        self::assertSame('2583000.0000', $bs['asset_total']->subtotal);
        self::assertSame('1000000.0000', $bs['equity']->subtotal);
        self::assertSame('1200000.0000', $bs['liability_equity_total']->subtotal);
        self::assertSame('1383000.0000', $pl['net_income']->subtotal);

        $next = $builder->applyNetIncomeCarryOver($bs, $pl);

        // Retained earnings now carries the net income line.
        $retained = $next['retained_earnings'];
        self::assertCount(1, $retained->lines);
        self::assertSame('当期純利益', $retained->lines[0]->label);
        self::assertSame('1383000.0000', $retained->lines[0]->amount);
        self::assertSame('1383000.0000', $retained->subtotal);
        self::assertSame('2383000.0000', $next['shareholders_equity']->subtotal);
        self::assertSame('2383000.0000', $next['equity']->subtotal);
        self::assertSame('2383000.0000', $next['equity_total']->subtotal);
        self::assertSame('2583000.0000', $next['liability_equity_total']->subtotal);
        // BS is now balanced: asset_total = liability_equity_total.
        self::assertSame($next['asset_total']->subtotal, $next['liability_equity_total']->subtotal);

        // Domain invariant holds.
        $builder->assertBalanced($next);
    }

    public function testApplyNetIncomeCarryOverLeavesBsAloneWhenEmpty(): void
    {
        $builder = new FinancialStatementBuilder();
        $plDefs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::ProfitAndLoss->value];
        $pl = $builder->build(FsKind::ProfitAndLoss, [
            self::row('S', '401', '売上', 'revenue', 'credit', '0', '1000'),
        ], [
            new AccountTitleFsMapping('S', FsKind::ProfitAndLoss, 'operating_revenue', 1, 10, null),
        ], $plDefs);

        self::assertSame([], $builder->applyNetIncomeCarryOver([], $pl));
    }

    public function testAssertBalancedIsSilentWhenBsEmpty(): void
    {
        $builder = new FinancialStatementBuilder();
        // No exception means success.
        $builder->assertBalanced([]);
        self::assertTrue(true);
    }

    public function testAssertBalancedThrowsWhenBsDoesNotBalance(): void
    {
        $builder = new FinancialStatementBuilder();
        $bsDefs = InMemoryFsSectionDefinitionRepository::jgaapStandard()[FsKind::BalanceSheet->value];

        // Asset 1000, no liability/equity — imbalance.
        $bs = $builder->build(FsKind::BalanceSheet, [
            self::row('CASH', '101', '現金', 'asset', 'debit', '1000', '0'),
        ], [
            new AccountTitleFsMapping('CASH', FsKind::BalanceSheet, 'current_asset', 1, 10, null),
        ], $bsDefs);

        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessage('financial_statement.bs_must_balance');
        $builder->assertBalanced($bs);
    }

    private static function row(
        string $id,
        string $code,
        string $name,
        string $category,
        string $normalSide,
        string $debit,
        string $credit,
    ): TrialBalanceRow {
        return TrialBalanceRow::compute(
            accountTitleId: $id,
            accountTitleCode: $code,
            accountTitleName: $name,
            accountCategory: $category,
            normalSide: $normalSide,
            debitTotal: $debit,
            creditTotal: $credit,
            lineCount: 1,
        );
    }
}
