<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use InvalidArgumentException;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Read-side UseCase: list every {@see SsManualAdjustment} recorded
 * for an (entity, fiscal term) tuple, ordered by sort_order ASC.
 */
final readonly class ListSsAdjustmentsUseCase
{
    public function __construct(
        private SsManualAdjustmentRepositoryInterface $repo,
    ) {
    }

    /**
     * @return list<SsManualAdjustment>
     */
    public function execute(string $entityId, string $fiscalTermId): array
    {
        if (!UlidGenerator::isValid($entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        return $this->repo->findByEntityAndFiscalTerm($entityId, $fiscalTermId);
    }
}
