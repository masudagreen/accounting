<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\TrialBalance;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;

#[CoversClass(TrialBalanceRow::class)]
final class TrialBalanceRowTest extends TestCase
{
    public function testComputesDebitBalanceForDebitNormalAccount(): void
    {
        $row = TrialBalanceRow::compute(
            accountTitleId: '01HW7K9B2QV7C8Y4ZACC0000001',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '12000.0000',
            creditTotal: '3000.0000',
            lineCount: 4,
        );

        self::assertSame('12000.0000', $row->debitTotal);
        self::assertSame('3000.0000', $row->creditTotal);
        self::assertSame('9000.0000', $row->balance);
        self::assertSame(4, $row->lineCount);
    }

    public function testComputesCreditBalanceForCreditNormalAccount(): void
    {
        $row = TrialBalanceRow::compute(
            accountTitleId: '01HW7K9B2QV7C8Y4ZACC0000002',
            accountTitleCode: '401',
            accountTitleName: '売上高',
            accountCategory: 'revenue',
            normalSide: 'credit',
            debitTotal: '500.0000',
            creditTotal: '8500.0000',
            lineCount: 7,
        );

        self::assertSame('8000.0000', $row->balance);
    }

    public function testBalanceCanGoNegativeForContraAccounts(): void
    {
        // A credit-normal account that ends up with a debit balance → contra
        $row = TrialBalanceRow::compute(
            accountTitleId: '01HW7K9B2QV7C8Y4ZACC0000003',
            accountTitleCode: '201',
            accountTitleName: '買掛金',
            accountCategory: 'liability',
            normalSide: 'credit',
            debitTotal: '500.0000',
            creditTotal: '300.0000',
            lineCount: 2,
        );

        self::assertSame('-200.0000', $row->balance);
    }

    public function testAddMergesTwoRowsOfSameAccount(): void
    {
        $snapshotPart = TrialBalanceRow::compute(
            accountTitleId: 'ACC',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '1000.0000',
            creditTotal: '200.0000',
            lineCount: 2,
        );
        $livePart = TrialBalanceRow::compute(
            accountTitleId: 'ACC',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '500.0000',
            creditTotal: '100.0000',
            lineCount: 3,
        );

        $merged = $snapshotPart->add($livePart);

        self::assertSame('1500.0000', $merged->debitTotal);
        self::assertSame('300.0000', $merged->creditTotal);
        self::assertSame('1200.0000', $merged->balance);
        self::assertSame(5, $merged->lineCount);
    }

    public function testAddRejectsDifferentAccounts(): void
    {
        $a = TrialBalanceRow::compute('A', '1', 'a', 'asset', 'debit', '0', '0', 0);
        $b = TrialBalanceRow::compute('B', '2', 'b', 'asset', 'debit', '0', '0', 0);

        $this->expectException(\InvalidArgumentException::class);

        $a->add($b);
    }

    public function testZeroTotalsProduceZeroBalance(): void
    {
        $row = TrialBalanceRow::compute(
            accountTitleId: 'A',
            accountTitleCode: '1',
            accountTitleName: 'a',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '0',
            creditTotal: '0',
            lineCount: 0,
        );

        self::assertSame('0.0000', $row->balance);
    }

    // -----------------------------------------------------------------
    // Phase B-3b: opening (前期繰越) folded into balance
    // -----------------------------------------------------------------

    public function testDefaultOpeningIsZeroAndBalanceUnchanged(): void
    {
        $row = TrialBalanceRow::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '1000.0000',
            creditTotal: '200.0000',
            lineCount: 2,
        );

        self::assertSame('0.0000', $row->openingBalance);
        self::assertSame('800.0000', $row->balance, 'No opening → period dr - cr only');
    }

    public function testOpeningIsAddedToBalanceForDebitNormalAccount(): void
    {
        $row = TrialBalanceRow::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '1593200.0000',
            creditTotal: '0.0000',
            lineCount: 1,
            openingBalance: '3897237.0000',
        );

        self::assertSame('3897237.0000', $row->openingBalance);
        // 3897237 + (1593200 - 0) = 5490437 → matches RC-8 ground truth
        self::assertSame('5490437.0000', $row->balance);
    }

    public function testOpeningIsAddedToBalanceForCreditNormalAccount(): void
    {
        // Credit-normal account (e.g. accounts payable). Opening is in the
        // natural side direction of the account category (positive = credit
        // balance), so balance = opening + (cr - dr).
        $row = TrialBalanceRow::compute(
            accountTitleId: 'B',
            accountTitleCode: '201',
            accountTitleName: '買掛金',
            accountCategory: 'liability',
            normalSide: 'credit',
            debitTotal: '300.0000',
            creditTotal: '500.0000',
            lineCount: 2,
            openingBalance: '1000.0000',
        );

        // 1000 + (500 - 300) = 1200
        self::assertSame('1200.0000', $row->balance);
    }

    public function testAddPreservesOpeningFromLeftHandRow(): void
    {
        // Snapshot already had opening folded in; live tail must not be
        // double-counted.
        $snapshot = TrialBalanceRow::compute(
            accountTitleId: 'ACC',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '1000.0000',
            creditTotal: '0.0000',
            lineCount: 1,
            openingBalance: '500.0000',
        );
        $live = TrialBalanceRow::compute(
            accountTitleId: 'ACC',
            accountTitleCode: '101',
            accountTitleName: '現金',
            accountCategory: 'asset',
            normalSide: 'debit',
            debitTotal: '300.0000',
            creditTotal: '50.0000',
            lineCount: 2,
            openingBalance: '0.0000',
        );

        $merged = $snapshot->add($live);

        self::assertSame('500.0000', $merged->openingBalance);
        // 500 + ((1000 + 300) - (0 + 50)) = 500 + 1250 = 1750
        self::assertSame('1750.0000', $merged->balance);
    }
}
