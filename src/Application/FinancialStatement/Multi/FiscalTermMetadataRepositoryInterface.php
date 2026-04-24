<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Multi;

/**
 * Repository that hydrates {@see FiscalTermMetadata} for the multi-period
 * use case.
 *
 * The method takes a list of ids and returns the subset it could resolve, so
 * callers can detect "the user asked for 3 terms but we could only find 2".
 */
interface FiscalTermMetadataRepositoryInterface
{
    /**
     * @param list<string> $ids
     * @return list<FiscalTermMetadata> Subset in insertion order by `ids`.
     */
    public function findByIds(array $ids): array;
}
