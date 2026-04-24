<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

#[CoversClass(GenerateFinancialStatementUseCase::class)]
final class GenerateFinancialStatementUseCaseTest extends TestCase
{
    private const ENT = 'ENT';
    private const TERM = 'TRM';

    public function testBuildsBalanceSheetAndProfitAndLossFromTrialBalance(): void
    {
        $useCase = $this->makeUseCase(function (InMemoryTrialBalanceQuery $q, InMemoryAccountTitleRepository $r): void {
            $this->seedChart($r);
            // Balanced transaction: cash 5000 debit ←→ sales 5000 credit
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '5000.0000');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '5000.0000');
            // Expense of 2000 with matching asset decrease
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-15'), 'ACC_COST', '501', '仕入', 'expense', 'debit', 'debit', '2000.0000');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-15'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'credit', '2000.0000');
        });

        $fs = $useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Net income = Revenue 5000 - Expense 2000 = 3000
        self::assertSame('3000.0000', $fs->totals['net_income']);

        // BS:
        // assets → 現金 balance = 5000 - 2000 = 3000
        self::assertArrayHasKey(Section::CODE_ASSETS, $fs->bs);
        self::assertSame('3000.0000', $fs->bs[Section::CODE_ASSETS]->subtotal);

        // equity → should include an appended "利益剰余金（当期純利益）" line
        // carrying 3000 so the BS balances.
        self::assertArrayHasKey(Section::CODE_EQUITY, $fs->bs);
        self::assertSame('3000.0000', $fs->bs[Section::CODE_EQUITY]->subtotal);

        // PL:
        self::assertSame('5000.0000', $fs->pl[Section::CODE_REVENUE]->subtotal);
        self::assertSame('2000.0000', $fs->pl[Section::CODE_EXPENSES]->subtotal);

        // BS identity: assets = liabilities + equity
        self::assertSame(
            $fs->bs[Section::CODE_ASSETS]->subtotal,
            $fs->bs[Section::CODE_EQUITY]->subtotal,
        );
    }

    public function testKindBsOnlyOmitsPlAndCs(): void
    {
        $useCase = $this->makeUseCase(function (InMemoryTrialBalanceQuery $q, InMemoryAccountTitleRepository $r): void {
            $this->seedChart($r);
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '100');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '100');
        });

        $fs = $useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertTrue($fs->hasBalanceSheet());
        self::assertFalse($fs->hasProfitAndLoss());
        self::assertFalse($fs->hasCashFlow());
    }

    public function testNetIncomeIsZeroWhenNoRevenueOrExpense(): void
    {
        $useCase = $this->makeUseCase(function (InMemoryTrialBalanceQuery $q, InMemoryAccountTitleRepository $r): void {
            $this->seedChart($r);
            // Pure balance-sheet transfer: cash ←→ 資本金
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '10000');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-05'), 'ACC_EQUITY', '301', '資本金', 'equity', 'credit', 'credit', '10000');
        });

        $fs = $useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame('0.0000', $fs->totals['net_income']);
        // Asset 10000 = Equity 10000 (資本金) + 0 (NI)
        self::assertSame('10000.0000', $fs->bs[Section::CODE_ASSETS]->subtotal);
        self::assertSame('10000.0000', $fs->bs[Section::CODE_EQUITY]->subtotal);
    }

    public function testCashFlowStubCapturesCashAccountsStartingWith11(): void
    {
        $useCase = $this->makeUseCase(function (InMemoryTrialBalanceQuery $q, InMemoryAccountTitleRepository $r): void {
            $r->seed(self::ENT, 'ACC_CASH', '110', '現金', 'asset', 'debit');
            $r->seed(self::ENT, 'ACC_SALES', '401', '売上', 'revenue', 'credit');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_CASH', '110', '現金', 'asset', 'debit', 'debit', '7500');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '7500');
        });

        $fs = $useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::CashFlow,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertTrue($fs->hasCashFlow());
        $operating = $fs->cs[Section::CODE_OPERATING_CF];
        self::assertCount(2, $operating->lines);
        // operating subtotal = net income 7500 + cash delta 7500 = 15000
        self::assertSame('15000.0000', $operating->subtotal);
    }

    public function testEnrichesRowsMissingCategoryViaAccountTitleLookup(): void
    {
        // When snapshot-origin rows arrive with empty category, the use case
        // should fill it in from the chart of accounts repo.
        $useCase = $this->makeUseCase(function (InMemoryTrialBalanceQuery $q, InMemoryAccountTitleRepository $r): void {
            $this->seedChart($r);
            // empty-category rows (simulate snapshot-only data)
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_CASH', '', '', '', 'debit', 'debit', '500');
            $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '', '', '', 'credit', 'credit', '500');
        });

        $fs = $useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Should have found ACC_CASH (asset) on the BS and ACC_SALES (revenue) on the PL.
        self::assertSame('500.0000', $fs->bs[Section::CODE_ASSETS]->subtotal);
        self::assertSame('500.0000', $fs->pl[Section::CODE_REVENUE]->subtotal);
    }

    /**
     * @param callable(InMemoryTrialBalanceQuery, InMemoryAccountTitleRepository): void $seed
     */
    private function makeUseCase(callable $seed): GenerateFinancialStatementUseCase
    {
        $query = new InMemoryTrialBalanceQuery();
        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $accounts = new InMemoryAccountTitleRepository();
        $seed($query, $accounts);
        $query->setLatestSnapshot(null);
        $tbUseCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());
        return new GenerateFinancialStatementUseCase(
            trialBalance: $tbUseCase,
            accounts: $accounts,
            clock: new FrozenClock(),
        );
    }

    private function seedChart(InMemoryAccountTitleRepository $r): void
    {
        $r->seed(self::ENT, 'ACC_CASH', '101', '現金', 'asset', 'debit');
        $r->seed(self::ENT, 'ACC_SALES', '401', '売上', 'revenue', 'credit');
        $r->seed(self::ENT, 'ACC_COST', '501', '仕入', 'expense', 'debit');
        $r->seed(self::ENT, 'ACC_EQUITY', '301', '資本金', 'equity', 'credit');
    }
}
