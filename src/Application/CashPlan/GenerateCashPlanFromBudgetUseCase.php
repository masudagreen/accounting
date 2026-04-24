<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use DateTimeImmutable;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * Bootstrap a new cash plan by copying the prior-period revenue /
 * expense totals (from a TrialBalance read model) into 12 equal monthly
 * buckets.
 *
 * Intentionally thin: the legacy plugin's budget engine is richer but
 * porting it whole would blow this wave. This gives users a sensible
 * starting point that they then edit in the UI.
 */
final readonly class GenerateCashPlanFromBudgetUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
        private QueryTrialBalanceUseCase $trialBalance,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $priorFrom,
        DateTimeImmutable $priorTo,
        string $name,
        string $openingBalance,
        string $currencyCode,
        string $createdBy,
    ): CreateCashPlanOutput {
        if (!UlidGenerator::isValid($entityId)) {
            throw ValidationException::withErrors(['entityId' => ['entityId must be a ULID.']]);
        }

        $tb = $this->trialBalance->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fiscalTermStartDate: $priorFrom,
            asOf: $priorTo,
        ));

        $entries = [];
        $order = 0;
        $planId = $this->ulids->generate();
        foreach ($tb->rows as $row) {
            $entry = $this->asEntry($row, $planId, $order);
            if ($entry === null) {
                continue;
            }
            $entries[] = $entry;
            $order++;
        }

        $now = $this->clock->getCurrentTime();
        $plan = new CashPlan(
            id: $planId,
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            name: $name,
            openingBalance: $openingBalance,
            currencyCode: strtoupper($currencyCode),
            notes: sprintf(
                'Generated from prior period %s..%s.',
                $priorFrom->format('Y-m-d'),
                $priorTo->format('Y-m-d'),
            ),
            entries: $entries,
            createdBy: $createdBy,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );
        $this->plans->save($plan);
        return new CreateCashPlanOutput($plan);
    }

    private function asEntry(TrialBalanceRow $row, string $planId, int $order): ?CashPlanEntry
    {
        $category = match ($row->accountCategory) {
            'revenue' => CashPlanCategory::OperatingIn,
            'expense', 'cost_of_sales', 'selling_admin' => CashPlanCategory::OperatingOut,
            'non_operating_income' => CashPlanCategory::FinancingIn,
            'non_operating_expense' => CashPlanCategory::FinancingOut,
            default => null,
        };
        if ($category === null) {
            return null;
        }
        $abs = str_starts_with($row->balance, '-') ? substr($row->balance, 1) : $row->balance;
        if (Decimal::compare($abs, '0.0000') === 0) {
            return null;
        }
        $perMonth = self::divideBy12($abs);
        /** @var list<string> $amounts */
        $amounts = array_fill(0, CashPlanEntry::MONTHS, $perMonth);
        return new CashPlanEntry(
            id: $this->ulids->generate(),
            cashPlanId: $planId,
            category: $category,
            label: sprintf('[%s] %s', $row->accountTitleCode, $row->accountTitleName),
            sortOrder: $order,
            monthlyAmounts: $amounts,
            memo: null,
        );
    }

    private static function divideBy12(string $v): string
    {
        if (function_exists('bcdiv')) {
            /** @var string */
            return bcdiv($v, '12', Decimal::SCALE);
        }
        $f = (float) $v / 12.0;
        return number_format($f, Decimal::SCALE, '.', '');
    }
}
