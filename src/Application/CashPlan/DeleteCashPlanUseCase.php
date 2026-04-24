<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;

/**
 * Soft-delete a cash plan. Idempotent: deleting an already-deleted plan
 * is a no-op so callers can safely retry.
 */
final readonly class DeleteCashPlanUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
    ) {
    }

    public function execute(string $id): void
    {
        $this->plans->delete($id);
    }
}
