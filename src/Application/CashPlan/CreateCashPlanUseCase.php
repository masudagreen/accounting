<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use InvalidArgumentException;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a fresh 資金繰り計画 for the (entity, fiscal term, name) triple.
 *
 * Name collisions within the same (entity, fiscal term) raise a
 * ValidationException so the HTTP layer can return 422 instead of 500.
 */
final readonly class CreateCashPlanUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateCashPlanInput $input): CreateCashPlanOutput
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

        $existing = $this->plans->findByEntityAndName(
            $input->entityId,
            $input->fiscalTermId,
            $input->name,
        );
        if ($existing !== null) {
            throw ValidationException::withErrors([
                'name' => [sprintf('a cash plan named "%s" already exists for this fiscal term.', $input->name)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $planId = $this->ulids->generate();

        $entries = [];
        foreach ($input->entries as $idx => $e) {
            $entries[] = $this->buildEntry($planId, $e, $idx);
        }

        $plan = new CashPlan(
            id: $planId,
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            name: $input->name,
            openingBalance: $input->openingBalance,
            currencyCode: strtoupper($input->currencyCode),
            notes: $input->notes,
            entries: $entries,
            createdBy: $input->createdBy,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );

        $this->plans->save($plan);
        return new CreateCashPlanOutput($plan);
    }

    private function buildEntry(string $planId, CashPlanEntryInput $e, int $idx): CashPlanEntry
    {
        $category = CashPlanCategory::tryFrom($e->category);
        if ($category === null) {
            throw ValidationException::withErrors([
                "entries.$idx.category" => [sprintf('unknown cash plan category "%s".', $e->category)],
            ]);
        }
        return new CashPlanEntry(
            id: $e->id ?? $this->ulids->generate(),
            cashPlanId: $planId,
            category: $category,
            label: $e->label,
            sortOrder: $e->sortOrder,
            monthlyAmounts: $e->monthlyAmounts,
            memo: $e->memo,
        );
    }
}
