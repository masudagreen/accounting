<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Repository port for {@see AccountTitleFsMapping}.
 *
 * Implementations read from `account_title_fs_mappings` keyed by entity.
 */
interface AccountTitleFsMappingRepositoryInterface
{
    /**
     * Return every mapping for the given entity. Ordered by
     * (fs_kind, sort_order, account_title_code).
     *
     * @return list<AccountTitleFsMapping>
     */
    public function findAllByEntity(string $entityId): array;
}
