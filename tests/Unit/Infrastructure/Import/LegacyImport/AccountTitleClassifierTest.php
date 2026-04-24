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
}
