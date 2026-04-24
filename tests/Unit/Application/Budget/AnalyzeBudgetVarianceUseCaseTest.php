<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceInput;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

#[CoversClass(AnalyzeBudgetVarianceUseCase::class)]
final class AnalyzeBudgetVarianceUseCaseTest extends TestCase
{
    private const ENTITY_ID = '01HAAAAAAAAAAAAAAAAAAAAAA1';
    private const FISCAL_ID = '01HAAAAAAAAAAAAAAAAAAAAAA2';
    private const SALES_ID  = '01HAAAAAAAAAAAAAAAAAAAAAC0';
    private const COGS_ID   = '01HAAAAAAAAAAAAAAAAAAAAAC1';
    private const SGA_ID    = '01HAAAAAAAAAAAAAAAAAAAAAC2';

    public function testProducesOneRowPerAccountWithVariance(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedBudget($repo);

        $query = new InMemoryTrialBalanceQuery();
        // 1 month elapsed: sales 1.5M actual vs 1.5M budget, cogs 500k actual vs 300k budget
        $this->pushLine($query, self::SALES_ID, '4000', '売上', 'revenue', 'credit', 'credit', '1500000.0000');
        $this->pushLine($query, self::COGS_ID, '5000', '仕入', 'expense', 'debit', 'debit', '500000.0000');
        $this->pushLine($query, self::SGA_ID, '5500', '販管費', 'expense', 'debit', 'debit', '50000.0000');

        $uc = new AnalyzeBudgetVarianceUseCase(
            budgets: $repo,
            trialBalance: new QueryTrialBalanceUseCase($query, new InMemoryTrialBalanceSnapshotRepository(), new FrozenClock()),
            clock: new FrozenClock(),
        );

        $result = $uc->execute(new AnalyzeBudgetVarianceInput(
            budgetId: $budget->id,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            asOf: new DateTimeImmutable('2026-04-30T00:00:00Z'),
        ));

        self::assertCount(3, $result->rows);
        $byId = [];
        foreach ($result->rows as $row) {
            $byId[$row->accountTitleId] = $row;
        }
        self::assertSame('1500000.0000', $byId[self::SALES_ID]->budgetAmount);
        self::assertSame('1500000.0000', $byId[self::SALES_ID]->actualAmount);
        self::assertSame('0.0000', $byId[self::SALES_ID]->varianceAmount);

        self::assertSame('300000.0000', $byId[self::COGS_ID]->budgetAmount);
        self::assertSame('500000.0000', $byId[self::COGS_ID]->actualAmount);
        self::assertSame('200000.0000', $byId[self::COGS_ID]->varianceAmount);
        self::assertTrue($byId[self::COGS_ID]->isOverBudget());

        self::assertSame('50000.0000', $byId[self::SGA_ID]->budgetAmount);
        self::assertSame('50000.0000', $byId[self::SGA_ID]->actualAmount);
        self::assertSame('100.00', $byId[self::SGA_ID]->usageRatePercent);
    }

    public function testActualWithoutBudgetStillSurfacesAsOverBudget(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedBudget($repo);

        $query = new InMemoryTrialBalanceQuery();
        // Unexpected expense hitting an account never budgeted.
        $this->pushLine($query, '01HAAAAAAAAAAAAAAAAAAAAAC9', '5999', '予備費', 'expense', 'debit', 'debit', '10000.0000');

        $uc = new AnalyzeBudgetVarianceUseCase(
            budgets: $repo,
            trialBalance: new QueryTrialBalanceUseCase($query, new InMemoryTrialBalanceSnapshotRepository(), new FrozenClock()),
            clock: new FrozenClock(),
        );

        $result = $uc->execute(new AnalyzeBudgetVarianceInput(
            budgetId: $budget->id,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            asOf: new DateTimeImmutable('2026-04-30T00:00:00Z'),
        ));
        $unplanned = null;
        foreach ($result->rows as $row) {
            if ($row->accountTitleId === '01HAAAAAAAAAAAAAAAAAAAAAC9') {
                $unplanned = $row;
            }
        }
        self::assertNotNull($unplanned);
        self::assertNull($unplanned->usageRatePercent);
        self::assertTrue($unplanned->isOverBudget());
    }

    public function testCumulativeBudgetScalesWithMonthsElapsed(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedBudget($repo);

        $query = new InMemoryTrialBalanceQuery();
        $uc = new AnalyzeBudgetVarianceUseCase(
            budgets: $repo,
            trialBalance: new QueryTrialBalanceUseCase($query, new InMemoryTrialBalanceSnapshotRepository(), new FrozenClock()),
            clock: new FrozenClock(),
        );

        // 6 months elapsed → budget-to-date = 6x monthly budget.
        $result = $uc->execute(new AnalyzeBudgetVarianceInput(
            budgetId: $budget->id,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            asOf: new DateTimeImmutable('2026-09-30T00:00:00Z'),
        ));

        $byId = [];
        foreach ($result->rows as $row) {
            $byId[$row->accountTitleId] = $row;
        }
        self::assertSame('9000000.0000', $byId[self::SALES_ID]->budgetAmount);
        self::assertSame('1800000.0000', $byId[self::COGS_ID]->budgetAmount);
        self::assertSame('300000.0000', $byId[self::SGA_ID]->budgetAmount);
    }

    private function seedBudget(InMemoryBudgetRepository $repo): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $budget = new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_ID,
            name: 'Plan 2026',
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
            notes: null,
            lineItems: [
                new BudgetLineItem(
                    id: '01HAAAAAAAAAAAAAAAAAAAAAB1',
                    budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB0',
                    accountTitleId: self::SALES_ID,
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '1500000.0000'),
                ),
                new BudgetLineItem(
                    id: '01HAAAAAAAAAAAAAAAAAAAAAB2',
                    budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB0',
                    accountTitleId: self::COGS_ID,
                    subAccountTitleId: null,
                    sortOrder: 1,
                    monthlyAmounts: array_fill(0, 12, '300000.0000'),
                ),
                new BudgetLineItem(
                    id: '01HAAAAAAAAAAAAAAAAAAAAAB3',
                    budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB0',
                    accountTitleId: self::SGA_ID,
                    subAccountTitleId: null,
                    sortOrder: 2,
                    monthlyAmounts: array_fill(0, 12, '50000.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($budget);
        return $budget;
    }

    private function pushLine(
        InMemoryTrialBalanceQuery $query,
        string $accountId,
        string $code,
        string $name,
        string $category,
        string $normalSide,
        string $side,
        string $amount,
    ): void {
        $query->addLine(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_ID,
            date: new DateTimeImmutable('2026-04-15T00:00:00Z'),
            accountId: $accountId,
            accountCode: $code,
            accountName: $name,
            category: $category,
            normalSide: $normalSide,
            side: $side,
            amount: $amount,
        );
    }
}
