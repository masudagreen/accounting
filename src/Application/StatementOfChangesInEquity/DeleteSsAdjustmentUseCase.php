<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;

/**
 * Hard-delete a manual adjustment. Adjustments are review-facing
 * scratch data — no downstream ledger row references them, so there
 * is no need for a soft-delete column.
 */
final readonly class DeleteSsAdjustmentUseCase
{
    public function __construct(
        private SsManualAdjustmentRepositoryInterface $repo,
    ) {
    }

    public function execute(string $id): void
    {
        $existing = $this->repo->findById($id);
        if ($existing === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('ss adjustment %s was not found.', $id)],
            ]);
        }
        $this->repo->delete($id);
    }
}
