<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

final readonly class UpdateCashPlanUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateCashPlanInput $input): CreateCashPlanOutput
    {
        $existing = $this->plans->findById($input->id);
        if ($existing === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('cash plan %s was not found.', $input->id)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $plan = $existing;

        if ($input->name !== null
            || $input->openingBalance !== null
            || $input->currencyCode !== null
            || $input->notes !== null) {
            $plan = $plan->withHeader(
                name: $input->name ?? $plan->name,
                openingBalance: $input->openingBalance ?? $plan->openingBalance,
                currencyCode: $input->currencyCode !== null ? strtoupper($input->currencyCode) : $plan->currencyCode,
                notes: $input->notes ?? $plan->notes,
                now: $now,
            );
        }
        if ($input->entries !== null) {
            $entries = [];
            foreach ($input->entries as $idx => $e) {
                $category = CashPlanCategory::tryFrom($e->category);
                if ($category === null) {
                    throw ValidationException::withErrors([
                        "entries.$idx.category" => [sprintf('unknown cash plan category "%s".', $e->category)],
                    ]);
                }
                $entries[] = new CashPlanEntry(
                    id: $e->id ?? $this->ulids->generate(),
                    cashPlanId: $plan->id,
                    category: $category,
                    label: $e->label,
                    sortOrder: $e->sortOrder,
                    monthlyAmounts: $e->monthlyAmounts,
                    memo: $e->memo,
                );
            }
            $plan = $plan->withEntries($entries, $now);
        }

        $this->plans->save($plan);
        return new CreateCashPlanOutput($plan);
    }
}
