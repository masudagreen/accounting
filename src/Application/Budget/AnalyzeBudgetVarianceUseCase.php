<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetVarianceAnalysis;
use Rucaro\Domain\Budget\BudgetVarianceRow;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * Orchestrates a Budget-vs-Actual variance analysis.
 *
 * Steps:
 *   1. Load the budget aggregate (fails with {@see ValidationException}
 *      if the ID is unknown).
 *   2. Determine how many fiscal months have elapsed between
 *      `fiscalTermStartDate` and `asOf`. This caps which monthly columns
 *      of the budget are summed into the "budget-to-date" figure.
 *   3. Delegate actuals to {@see QueryTrialBalanceUseCase} so the snapshot
 *      + tail caching strategy is reused.
 *   4. Merge budget + actual by accountTitleId; produce one
 *      {@see BudgetVarianceRow} per account regardless of which side
 *      contributed.
 */
final readonly class AnalyzeBudgetVarianceUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
        private QueryTrialBalanceUseCase $trialBalance,
        private ClockInterface $clock,
    ) {
    }

    public function execute(AnalyzeBudgetVarianceInput $input): BudgetVarianceAnalysis
    {
        if (!UlidGenerator::isValid($input->budgetId)) {
            throw new InvalidArgumentException('budgetId must be a ULID.');
        }

        $budget = $this->budgets->findById($input->budgetId);
        if ($budget === null) {
            throw ValidationException::withErrors([
                'budgetId' => [sprintf('budget %s was not found.', $input->budgetId)],
            ]);
        }

        $monthsElapsed = $this->monthsElapsed($input->fiscalTermStartDate, $input->asOf);

        $trialBalance = $this->trialBalance->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $budget->entityId,
            fiscalTermId: $budget->fiscalTermId,
            fiscalTermStartDate: $input->fiscalTermStartDate,
            asOf: $input->asOf,
            currencyCode: $input->currencyCode,
        ));

        $rows = $this->buildRows($budget->lineItems, $monthsElapsed, $trialBalance);

        return new BudgetVarianceAnalysis(
            budgetId: $budget->id,
            entityId: $budget->entityId,
            fiscalTermId: $budget->fiscalTermId,
            budgetName: $budget->name,
            status: $budget->status,
            periodFrom: $input->fiscalTermStartDate,
            periodTo: $input->asOf,
            currencyCode: $input->currencyCode,
            rows: $rows,
            generatedAt: $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC')),
        );
    }

    /**
     * Number of fiscal months covered by the actuals window. Clamped to
     * 1..12 so a mid-month `asOf` still pulls the entire month's budget
     * (legacy BudgetOutput did the same for "消化率" comparisons).
     */
    private function monthsElapsed(DateTimeImmutable $start, DateTimeImmutable $asOf): int
    {
        if ($asOf < $start) {
            return 1;
        }
        $years = (int) $asOf->format('Y') - (int) $start->format('Y');
        $months = (int) $asOf->format('n') - (int) $start->format('n');
        $elapsed = ($years * 12) + $months + 1;
        return max(1, min(12, $elapsed));
    }

    /**
     * @param list<\Rucaro\Domain\Budget\BudgetLineItem> $lineItems
     * @return list<BudgetVarianceRow>
     */
    private function buildRows(array $lineItems, int $monthsElapsed, TrialBalance $trialBalance): array
    {
        /** @var array<string, string> $budgetByAccount */
        $budgetByAccount = [];
        foreach ($lineItems as $li) {
            $cum = $li->cumulativeAmount($monthsElapsed);
            $current = $budgetByAccount[$li->accountTitleId] ?? '0.0000';
            $budgetByAccount[$li->accountTitleId] = Decimal::add($current, $cum);
        }

        /** @var array<string, TrialBalanceRow> $actualByAccount */
        $actualByAccount = [];
        foreach ($trialBalance->rows as $row) {
            $actualByAccount[$row->accountTitleId] = $row;
        }

        $accountIds = array_values(array_unique(array_merge(
            array_keys($budgetByAccount),
            array_keys($actualByAccount),
        )));

        $rows = [];
        foreach ($accountIds as $accountId) {
            $budgetAmt = $budgetByAccount[$accountId] ?? '0.0000';
            $actualRow = $actualByAccount[$accountId] ?? null;
            $actualAmt = $actualRow?->balance ?? '0.0000';
            $rows[] = BudgetVarianceRow::compute(
                accountTitleId: $accountId,
                accountTitleCode: $actualRow?->accountTitleCode ?? '',
                accountTitleName: $actualRow?->accountTitleName ?? '',
                budgetAmount: $budgetAmt,
                actualAmount: $actualAmt,
            );
        }

        usort(
            $rows,
            static fn (BudgetVarianceRow $a, BudgetVarianceRow $b): int
                => strcmp($a->accountTitleCode, $b->accountTitleCode)
                ?: strcmp($a->accountTitleId, $b->accountTitleId),
        );

        return $rows;
    }
}
