<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use InvalidArgumentException;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a fresh budget (予算) in {@see BudgetStatus::Draft}.
 *
 * Name collisions within the same (entity, fiscal term) surface as
 * {@see ValidationException} so the HTTP layer can return 422 instead of
 * a 500 from the UNIQUE constraint.
 */
final readonly class CreateBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateBudgetInput $input): BudgetOutput
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->createdBy)) {
            throw new InvalidArgumentException('createdBy must be a ULID.');
        }

        $existing = $this->budgets->findByEntityAndName(
            $input->entityId,
            $input->fiscalTermId,
            $input->name,
        );
        if ($existing !== null) {
            throw ValidationException::withErrors([
                'name' => [sprintf('a budget named "%s" already exists for this fiscal term.', $input->name)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $budgetId = $this->ulids->generate();

        $lineItems = [];
        foreach ($input->lineItems as $idx => $li) {
            $lineItems[] = $this->buildLineItem($budgetId, $li, $idx);
        }

        $budget = new Budget(
            id: $budgetId,
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            name: $input->name,
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
            notes: $input->notes,
            lineItems: $lineItems,
            createdBy: $input->createdBy,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );

        $this->budgets->save($budget);
        return new BudgetOutput($budget);
    }

    private function buildLineItem(string $budgetId, BudgetLineItemInput $li, int $idx): BudgetLineItem
    {
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
        return new BudgetLineItem(
            id: $li->id ?? $this->ulids->generate(),
            budgetId: $budgetId,
            accountTitleId: $li->accountTitleId,
            subAccountTitleId: $li->subAccountTitleId,
            sortOrder: $li->sortOrder,
            monthlyAmounts: $li->monthlyAmounts,
            memo: $li->memo,
        );
    }
}
