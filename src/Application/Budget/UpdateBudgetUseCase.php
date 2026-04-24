<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Update the header and/or line items of an existing Draft budget.
 *
 * Status-transition guard lives in the domain ({@see \Rucaro\Domain\Budget\Budget}),
 * so this UseCase just routes the requested fields through the aggregate.
 */
final readonly class UpdateBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateBudgetInput $input): BudgetOutput
    {
        $existing = $this->budgets->findById($input->id);
        if ($existing === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('budget %s was not found.', $input->id)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $budget = $existing;

        if ($input->name !== null || $input->notes !== null) {
            $budget = $budget->withHeader(
                name: $input->name ?? $budget->name,
                notes: $input->notes ?? $budget->notes,
                now: $now,
            );
        }

        if ($input->lineItems !== null) {
            $rebuilt = [];
            foreach ($input->lineItems as $idx => $li) {
                if (!UlidGenerator::isValid($li->accountTitleId)) {
                    throw ValidationException::withErrors([
                        "lineItems.$idx.accountTitleId" => ['accountTitleId must be a ULID.'],
                    ]);
                }
                if ($li->subAccountTitleId !== null && !UlidGenerator::isValid($li->subAccountTitleId)) {
                    throw ValidationException::withErrors([
                        "lineItems.$idx.subAccountTitleId" => ['subAccountTitleId must be a ULID when provided.'],
                    ]);
                }
                $rebuilt[] = new BudgetLineItem(
                    id: $li->id ?? $this->ulids->generate(),
                    budgetId: $budget->id,
                    accountTitleId: $li->accountTitleId,
                    subAccountTitleId: $li->subAccountTitleId,
                    sortOrder: $li->sortOrder,
                    monthlyAmounts: $li->monthlyAmounts,
                    memo: $li->memo,
                );
            }
            $budget = $budget->withLineItems($rebuilt, $now);
        }

        $this->budgets->save($budget);
        return new BudgetOutput($budget);
    }
}
