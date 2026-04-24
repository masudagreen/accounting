<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use InvalidArgumentException;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Persist a fresh {@see SsManualAdjustment} row. Fresh rows always
 * receive a newly-minted ULID so callers never have to fabricate one.
 */
final readonly class CreateSsAdjustmentUseCase
{
    public function __construct(
        private SsManualAdjustmentRepositoryInterface $repo,
        private UlidGenerator $ulids,
    ) {
    }

    public function execute(CreateSsAdjustmentInput $input): SsAdjustmentOutput
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }

        $adjustment = new SsManualAdjustment(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            sectionCode: $input->sectionCode,
            changeType: $input->changeType,
            amount: $input->amount,
            label: $input->label,
            sortOrder: $input->sortOrder,
            notes: $input->notes,
        );
        $this->repo->save($adjustment);
        return new SsAdjustmentOutput($adjustment);
    }
}
