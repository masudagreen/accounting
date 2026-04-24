<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

/**
 * Repository port for {@see AccountTitleCsMapping}.
 *
 * Implementations read from `account_title_cs_mappings` keyed by entity.
 */
interface AccountTitleCsMappingRepositoryInterface
{
    /**
     * Return every mapping for the given entity. Ordered by
     * (flow_category, sort_order, account_title_code).
     *
     * @return list<AccountTitleCsMapping>
     */
    public function findAllByEntity(string $entityId): array;
}
