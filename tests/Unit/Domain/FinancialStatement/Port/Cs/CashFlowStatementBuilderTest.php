<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatement\Port\Cs;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMapping;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsFlowCategory;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Port\Cs\Service\CashFlowStatementBuilder;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\Cs\InMemoryCsSectionDefinitionRepository;

#[CoversClass(CashFlowStatementBuilder::class)]
#[CoversClass(CsSectionDefinition::class)]
#[CoversClass(CsFlowCategory::class)]
#[CoversClass(AccountTitleCsMapping::class)]
final class CashFlowStatementBuilderTest extends TestCase
{
    public function testPretaxOnlyNoAdjustmentsEqualsOperatingCf(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // No mappings, no working capital. Operating CF = pretax income only.
        $cs = $builder->build(
            periodRows: [],
            priorRows: [],
            mappings: [],
            definitions: $defs,
            pretaxIncome: '100000',
            beginningCash: '0',
        );

        self::assertSame('100000.0000', $cs['operating_pretax_income']->subtotal);
        self::assertSame('100000.0000', $cs['operating_cf']->subtotal);
        self::assertSame('100000.0000', $cs['operating_cf_subtotal']->subtotal);
        self::assertSame('100000.0000', $cs['operating_cf_total']->subtotal);
        self::assertSame('0.0000',      $cs['investing_cf_total']->subtotal);
        self::assertSame('0.0000',      $cs['financing_cf_total']->subtotal);
        self::assertSame('100000.0000', $cs['net_change_in_cash']->subtotal);
        self::assertSame('0.0000',      $cs['beginning_cash']->subtotal);
        self::assertSame('100000.0000', $cs['ending_cash']->subtotal);
    }

    public function testWorkingCapitalReceivablesIncreaseReducesCashFlow(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // 売掛金 500 increase → 運転資本から 500 減算 (= -500 under wc_receivables).
        $rows = [
            self::row('AR', '121', '売掛金', 'asset', 'debit', '500', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('AR', 'wc_receivables', CsFlowCategory::Operating, 1, true, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '1000', '0');

        self::assertSame('-500.0000', $cs['wc_receivables']->subtotal);
        self::assertSame('500.0000',  $cs['operating_cf']->subtotal);       // 1000 - 500
        self::assertSame('500.0000',  $cs['operating_cf_total']->subtotal);
    }

    public function testNonCashDepreciationIsAddedBack(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // 減価償却費 200 is a non-cash expense — add back to operating.
        $rows = [
            self::row('DEP', '531', '減価償却費', 'expense', 'debit', '200', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('DEP', 'depreciation', CsFlowCategory::Operating, 1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '1000', '0');

        self::assertSame('200.0000',  $cs['depreciation']->subtotal);
        self::assertSame('1200.0000', $cs['operating_cf']->subtotal);
        self::assertSame('1200.0000', $cs['operating_cf_total']->subtotal);
    }

    public function testInvestingPpePurchaseOutflow(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // 有形固定資産の取得 3000 is an outflow — mapped with sign=-1.
        $rows = [
            self::row('PPE', '201', '建物', 'asset', 'debit', '3000', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('PPE', 'investing_ppe_purchase', CsFlowCategory::Investing, -1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '0', '5000');

        self::assertSame('-3000.0000', $cs['investing_ppe_purchase']->subtotal);
        self::assertSame('-3000.0000', $cs['investing_cf']->subtotal);
        self::assertSame('-3000.0000', $cs['investing_cf_total']->subtotal);
        // Beginning cash 5000 + change -3000 → 2000 ending.
        self::assertSame('-3000.0000', $cs['net_change_in_cash']->subtotal);
        self::assertSame('5000.0000',  $cs['beginning_cash']->subtotal);
        self::assertSame('2000.0000',  $cs['ending_cash']->subtotal);
    }

    public function testFinancingDebtProceedsInflow(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // 新規借入 2000 → financing CF + 2000.
        $rows = [
            self::row('DEBT', '301', '短期借入金', 'liability', 'credit', '0', '2000'),
        ];
        $mappings = [
            new AccountTitleCsMapping('DEBT', 'financing_debt_proceeds', CsFlowCategory::Financing, 1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '0', '0');

        self::assertSame('2000.0000', $cs['financing_debt_proceeds']->subtotal);
        self::assertSame('2000.0000', $cs['financing_cf']->subtotal);
        self::assertSame('2000.0000', $cs['financing_cf_total']->subtotal);
    }

    public function testDividendsPaidDecreasesFinancingCf(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        // 配当金支払 1000 → financing CF -1000.
        $rows = [
            self::row('DIV', '331', '配当金', 'equity', 'debit', '1000', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('DIV', 'financing_dividends_paid', CsFlowCategory::Financing, -1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '0', '0');

        self::assertSame('-1000.0000', $cs['financing_dividends_paid']->subtotal);
        self::assertSame('-1000.0000', $cs['financing_cf_total']->subtotal);
    }

    public function testInterestReceivedAndPaidAppearBelowSubtotal(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        $rows = [
            self::row('IR', '411', '受取利息', 'revenue', 'credit', '0', '300'),
            self::row('IP', '511', '支払利息', 'expense', 'debit',  '200', '0'),
            self::row('TAX','591', '法人税等',  'expense', 'debit',  '500', '0'),
        ];
        $mappings = [
            // Interest & tax feed section codes directly (not through a parent),
            // because these live below the 小計 line in the legal template.
            new AccountTitleCsMapping('IR',  'interest_received', CsFlowCategory::Operating, 1, false, 10, null),
            new AccountTitleCsMapping('IP',  'interest_paid',     CsFlowCategory::Operating, 1, false, 10, null),
            new AccountTitleCsMapping('TAX', 'tax_paid',          CsFlowCategory::Operating, 1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '1000', '0');

        self::assertSame('1000.0000', $cs['operating_cf_subtotal']->subtotal);
        self::assertSame('300.0000',  $cs['interest_received']->subtotal);
        self::assertSame('200.0000',  $cs['interest_paid']->subtotal);
        self::assertSame('500.0000',  $cs['tax_paid']->subtotal);
        // 1000 (小計) + 300 - 200 - 500 = 600
        self::assertSame('600.0000', $cs['operating_cf_total']->subtotal);
    }

    public function testBeginningCashPlusChangeEqualsEndingCashInvariant(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        $rows = [
            self::row('DEP',  '531', '減価償却費',   'expense',  'debit', '100', '0'),
            self::row('PPE',  '201', '建物',         'asset',    'debit', '300', '0'),
            self::row('DEBT', '301', '短期借入金',   'liability','credit', '0',  '150'),
        ];
        $mappings = [
            new AccountTitleCsMapping('DEP',  'depreciation',             CsFlowCategory::Operating, 1, false, 10, null),
            new AccountTitleCsMapping('PPE',  'investing_ppe_purchase',   CsFlowCategory::Investing, -1, false, 10, null),
            new AccountTitleCsMapping('DEBT', 'financing_debt_proceeds',  CsFlowCategory::Financing, 1, false, 10, null),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '500', '1000');

        // Operating: 500 pretax + 100 depreciation = 600
        // Investing: -300, Financing: +150
        // Net change = 600 - 300 + 150 = 450
        // Ending = 1000 + 450 = 1450
        self::assertSame('600.0000',  $cs['operating_cf_total']->subtotal);
        self::assertSame('-300.0000', $cs['investing_cf_total']->subtotal);
        self::assertSame('150.0000',  $cs['financing_cf_total']->subtotal);
        self::assertSame('450.0000',  $cs['net_change_in_cash']->subtotal);
        self::assertSame('1000.0000', $cs['beginning_cash']->subtotal);
        self::assertSame('1450.0000', $cs['ending_cash']->subtotal);
    }

    public function testUnmappedAccountsAreIgnored(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        $rows = [
            self::row('X', '999', 'noise', 'expense', 'debit', '99999', '0'),
        ];
        // No mapping for account X.
        $cs = $builder->build($rows, [], [], $defs, '0', '0');

        self::assertSame('0.0000', $cs['operating_cf_total']->subtotal);
        self::assertSame('0.0000', $cs['ending_cash']->subtotal);
    }

    public function testUnknownSectionCodeIsSkipped(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        $rows = [
            self::row('X', '999', 'x', 'asset', 'debit', '100', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('X', 'unknown_section', CsFlowCategory::Operating, 1, false, 10, null),
        ];
        $cs = $builder->build($rows, [], $mappings, $defs, '0', '0');

        self::assertSame('0.0000', $cs['operating_cf_total']->subtotal);
    }

    public function testDisplayLabelOverridesAccountName(): void
    {
        $builder = new CashFlowStatementBuilder();
        $defs = InMemoryCsSectionDefinitionRepository::jgaapStandard();

        $rows = [
            self::row('DEP', '531', '減価償却費', 'expense', 'debit', '100', '0'),
        ];
        $mappings = [
            new AccountTitleCsMapping('DEP', 'depreciation', CsFlowCategory::Operating, 1, false, 10, '償却費（ラベル上書き）'),
        ];

        $cs = $builder->build($rows, [], $mappings, $defs, '0', '0');

        self::assertCount(1, $cs['depreciation']->lines);
        self::assertSame('償却費（ラベル上書き）', $cs['depreciation']->lines[0]->label);
    }

    public function testFormulaParsingHandlesMixedSigns(): void
    {
        $def = new CsSectionDefinition(
            'operating_cf_total',
            null,
            '営業活動によるキャッシュフロー',
            29,
            false,
            true,
            '+operating_cf_subtotal+interest_received-interest_paid-tax_paid',
        );
        self::assertSame(
            [
                [1, 'operating_cf_subtotal'],
                [1, 'interest_received'],
                [-1, 'interest_paid'],
                [-1, 'tax_paid'],
            ],
            $def->parsedFormula(),
        );
    }

    public function testCsFlowCategoryFromString(): void
    {
        self::assertSame(CsFlowCategory::Operating, CsFlowCategory::fromString('operating'));
        self::assertSame(CsFlowCategory::Investing, CsFlowCategory::fromString('INV'));
        self::assertSame(CsFlowCategory::Financing, CsFlowCategory::fromString('Financing'));
    }

    public function testCsFlowCategoryRejectsUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CsFlowCategory::fromString('bogus');
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
