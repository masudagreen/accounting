<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;

/**
 * Update selected fields on an existing {@see SsManualAdjustment}.
 * Missing fields fall through via {@see SsManualAdjustment::with()}.
 */
final readonly class UpdateSsAdjustmentUseCase
{
    public function __construct(
        private SsManualAdjustmentRepositoryInterface $repo,
    ) {
    }

    public function execute(UpdateSsAdjustmentInput $input): SsAdjustmentOutput
    {
        $existing = $this->repo->findById($input->id);
        if ($existing === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('ss adjustment %s was not found.', $input->id)],
            ]);
        }
        $updated = $existing->with(
            sectionCode: $input->sectionCode,
            changeType: $input->changeType,
            amount: $input->amount,
            label: $input->label,
            sortOrder: $input->sortOrder,
            notes: $input->notes,
        );
        $this->repo->save($updated);
        return new SsAdjustmentOutput($updated);
    }
}
