<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Import\LegacyImport;

use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Import\LegacyImport\AccountTitleClassifier;

final class AccountTitleClassifierTest extends TestCase
{
    public function testClassifyKnownAssetCode(): void
    {
        [$cat, $side, $label] = AccountTitleClassifier::classify('cash');
        self::assertSame('asset', $cat);
        self::assertSame('debit', $side);
        self::assertSame('現金', $label);
    }

    public function testClassifyKnownLiabilityCode(): void
    {
        [$cat, $side] = AccountTitleClassifier::classify('accruedExpenses');
        self::assertSame('liability', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyKnownRevenueCode(): void
    {
        [$cat, $side] = AccountTitleClassifier::classify('netSales');
        self::assertSame('revenue', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyKnownExpenseCode(): void
    {
        [$cat, $side] = AccountTitleClassifier::classify('rents');
        self::assertSame('expense', $cat);
        self::assertSame('debit', $side);
    }

    public function testClassifyUnknownCodeFallsBackToExpenseDebit(): void
    {
        [$cat, $side, $label] = AccountTitleClassifier::classify('someNewAccountCode');
        self::assertSame('expense', $cat);
        self::assertSame('debit', $side);
        // Unknown codes echo the legacy code back as the label so the
        // operator can spot and backfill the classifier map.
        self::assertSame('someNewAccountCode', $label);
    }

    public function testIsKnownBooleans(): void
    {
        self::assertTrue(AccountTitleClassifier::isKnown('cash'));
        self::assertFalse(AccountTitleClassifier::isKnown('nonExistent'));
    }

    public function testKnownCodesCoversTheLegacyJournalUniverse(): void
    {
        // Minimum set observed in the 1613 legacy journals across both
        // entities. If a legacy export ever references a code outside this
        // set, `classify()` falls back to expense/debit (safe but visible).
        $expected = [
            'cash', 'ordinaryDeposit', 'accountsReceivable',
            'accruedExpenses', 'shortTermLoansPayable', 'depositePayable',
            'corporationTaxesPayable', 'consumptionTaxesRepayable',
            'netSales', 'miscellaneousIncome', 'interestAndDiscountReceived',
            'conferenceExpense', 'suppliesExpenses', 'correspondenceExpenses',
            'transportationExpenses', 'entertainmentExpenses',
            'directorsCompensations', 'legalWelfareExpenses', 'welfareExpenses',
            'insuranceExpenses', 'miscellaneousExpenses', 'badMiscellaneousExpenses',
            'taxesAndDues', 'rents', 'booksExpense', 'commissionPaid', 'repair',
            'waterPowerExpenses', 'corporateInhabitantAndEnterpriseTax',
            'contribution',
        ];
        $known = AccountTitleClassifier::knownCodes();
        foreach ($expected as $code) {
            self::assertContains($code, $known, sprintf('classifier missing %s', $code));
        }
    }

    // ---------------------------------------------------------------
    // F-3: classifyFromJsonTree — derive (category, normal_side) from
    // the master JSON tree node + its top-level group code.
    // ---------------------------------------------------------------

    public function testClassifyFromJsonTreeAssetsTopLevel(): void
    {
        $node = ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'];
        [$cat, $side, $label] = AccountTitleClassifier::classifyFromJsonTree($node, 'assets');
        self::assertSame('asset', $cat);
        self::assertSame('debit', $side);
        self::assertSame('現金', $label);
    }

    public function testClassifyFromJsonTreeLiabilitiesTopLevel(): void
    {
        $node = ['vars' => ['idTarget' => 'shortTermLoansPayable', 'flagDebit' => 0], 'strTitle' => '短期借入金'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'liabilities');
        self::assertSame('liability', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyFromJsonTreeNetAssetsTopLevel(): void
    {
        $node = ['vars' => ['idTarget' => 'commonStock', 'flagDebit' => 0], 'strTitle' => '資本金'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'netAssets');
        self::assertSame('equity', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyFromJsonTreeSalesTopLevelGivesRevenue(): void
    {
        $node = ['vars' => ['idTarget' => 'netSales', 'flagDebit' => 0], 'strTitle' => '売上高'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'sales');
        self::assertSame('revenue', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyFromJsonTreeNonOperatingIncomeIsRevenue(): void
    {
        $node = ['vars' => ['idTarget' => 'miscellaneousIncome', 'flagDebit' => 0], 'strTitle' => '雑収入'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'nonOperatingIncome');
        self::assertSame('revenue', $cat);
        self::assertSame('credit', $side);
    }

    public function testClassifyFromJsonTreeCostOfSalesIsExpense(): void
    {
        $node = ['vars' => ['idTarget' => 'goodsPurcheses', 'flagDebit' => 1], 'strTitle' => '当期商品仕入高'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'costOfSales');
        self::assertSame('expense', $cat);
        self::assertSame('debit', $side);
    }

    public function testClassifyFromJsonTreeSGAIsExpense(): void
    {
        $node = ['vars' => ['idTarget' => 'rents', 'flagDebit' => 1], 'strTitle' => '地代家賃'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'sellingGeneralAndAdministrationExpenses');
        self::assertSame('expense', $cat);
        self::assertSame('debit', $side);
    }

    public function testClassifyFromJsonTreeRespectsFlagDebitForContraAccounts(): void
    {
        // 売上値引高 lives inside the `sales` group but its normal side is
        // debit (it is a contra-revenue). Tree carries flagDebit=1 → side=debit.
        $node = ['vars' => ['idTarget' => 'salesAllowance', 'flagDebit' => 1], 'strTitle' => '売上値引高'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'sales');
        self::assertSame('revenue', $cat);
        self::assertSame('debit', $side);
    }

    public function testClassifyFromJsonTreeUnknownTopFallsBackToExpense(): void
    {
        $node = ['vars' => ['idTarget' => 'mystery', 'flagDebit' => 1], 'strTitle' => '謎'];
        [$cat, $side] = AccountTitleClassifier::classifyFromJsonTree($node, 'someUnknownGroup');
        self::assertSame('expense', $cat);
        self::assertSame('debit', $side);
    }

    // ---------------------------------------------------------------
    // F-3: extractLeavesFromTree — walk a decoded tree and return
    // ordered list of [code, label, category, normal_side, sort_order].
    // Subtotal nodes (suffix Sum / Net) are excluded.
    // ---------------------------------------------------------------

    public function testExtractLeavesFromMinimalBsTree(): void
    {
        $tree = [
            [
                'vars' => ['idTarget' => 'assets', 'flagDebit' => 1],
                'strTitle' => '資産',
                'child' => [
                    [
                        'vars' => ['idTarget' => 'currentAssets', 'flagDebit' => 1],
                        'strTitle' => '流動資産',
                        'child' => [
                            ['vars' => ['idTarget' => 'cash', 'flagDebit' => 1], 'strTitle' => '現金'],
                            ['vars' => ['idTarget' => 'ordinaryDeposit', 'flagDebit' => 1], 'strTitle' => '普通預金'],
                            ['vars' => ['idTarget' => 'currentAssetsSum', 'flagDebit' => 1], 'strTitle' => '流動資産合計'],
                        ],
                    ],
                ],
            ],
            ['vars' => ['idTarget' => 'assetsSum', 'flagDebit' => 1], 'strTitle' => '資産の部合計'],
        ];

        $leaves = AccountTitleClassifier::extractLeavesFromTree($tree);

        // Subtotals dropped, real leaves kept in order.
        self::assertCount(2, $leaves);
        self::assertSame('cash', $leaves[0]['code']);
        self::assertSame('現金', $leaves[0]['label']);
        self::assertSame('asset', $leaves[0]['category']);
        self::assertSame('debit', $leaves[0]['normal_side']);
        self::assertSame('ordinaryDeposit', $leaves[1]['code']);
    }

    public function testExtractLeavesSkipsSubtotalAndNetSuffixes(): void
    {
        $tree = [
            [
                'vars' => ['idTarget' => 'sales', 'flagDebit' => 0],
                'strTitle' => '売上高',
                'child' => [
                    ['vars' => ['idTarget' => 'netSales', 'flagDebit' => 0], 'strTitle' => '売上高'],
                    ['vars' => ['idTarget' => 'salesSum', 'flagDebit' => 0], 'strTitle' => '売上高合計'],
                ],
            ],
            ['vars' => ['idTarget' => 'salesSum', 'flagDebit' => 0], 'strTitle' => '売上高合計'],
            ['vars' => ['idTarget' => 'grossProfitOrLossNet', 'flagDebit' => 0], 'strTitle' => '売上総利益'],
        ];
        $leaves = AccountTitleClassifier::extractLeavesFromTree($tree);
        self::assertCount(1, $leaves);
        self::assertSame('netSales', $leaves[0]['code']);
        self::assertSame('revenue', $leaves[0]['category']);
    }
}
