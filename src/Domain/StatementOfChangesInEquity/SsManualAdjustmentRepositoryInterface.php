<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

/**
 * Repository port for {@see SsManualAdjustment} rows.
 *
 * The infrastructure-side adapter ({@see \Rucaro\Infrastructure\StatementOfChangesInEquity\PdoSsManualAdjustmentRepository})
 * upserts and lists by (entity × fiscal term). We do not expose a
 * free-form query surface: adjustments are only ever consumed by the
 * builder in whole-period batches, and leaking arbitrary predicates
 * here tends to attract drive-by features that the domain does not
 * actually want.
 */
interface SsManualAdjustmentRepositoryInterface
{
    public function save(SsManualAdjustment $adjustment): void;

    public function findById(string $id): ?SsManualAdjustment;

    /**
     * @return list<SsManualAdjustment> ordered by `sort_order ASC, id ASC`.
     */
    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array;

    public function delete(string $id): void;
}
