<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

use DateTimeImmutable;
use Rucaro\Support\Decimal\Decimal;

/**
 * Read model for a Budget-vs-Actual variance report.
 *
 * Produced by {@see \Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase}
 * from a single {@see Budget} plus a trial-balance roll-up for the same
 * entity + fiscal term.
 */
final readonly class BudgetVarianceAnalysis
{
    /**
     * @param list<BudgetVarianceRow> $rows
     */
    public function __construct(
        public string $budgetId,
        public string $entityId,
        public string $fiscalTermId,
        public string $budgetName,
        public BudgetStatus $status,
        public DateTimeImmutable $periodFrom,
        public DateTimeImmutable $periodTo,
        public string $currencyCode,
        public array $rows,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    public function totalBudget(): string
    {
        $sum = '0.0000';
        foreach ($this->rows as $row) {
            $sum = Decimal::add($sum, $row->budgetAmount);
        }
        return Decimal::normalize($sum);
    }

    public function totalActual(): string
    {
        $sum = '0.0000';
        foreach ($this->rows as $row) {
            $sum = Decimal::add($sum, $row->actualAmount);
        }
        return Decimal::normalize($sum);
    }

    public function totalVariance(): string
    {
        $sum = '0.0000';
        foreach ($this->rows as $row) {
            $sum = Decimal::add($sum, $row->varianceAmount);
        }
        return Decimal::normalize($sum);
    }

    /**
     * Only the rows that are over budget.
     *
     * @return list<BudgetVarianceRow>
     */
    public function overBudgetRows(): array
    {
        return array_values(array_filter(
            $this->rows,
            static fn (BudgetVarianceRow $r): bool => $r->isOverBudget(),
        ));
    }

    /**
     * Only the rows that are under budget.
     *
     * @return list<BudgetVarianceRow>
     */
    public function underBudgetRows(): array
    {
        return array_values(array_filter(
            $this->rows,
            static fn (BudgetVarianceRow $r): bool => $r->isUnderBudget(),
        ));
    }
}
