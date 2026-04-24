<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;

/**
 * Read port to aggregate {@see TaxableTransaction} rows from the
 * journal line store for a single period.
 */
interface TaxableTransactionQueryInterface
{
    /**
     * @return list<TaxableTransaction>
     */
    public function findByPeriod(
        string $entityId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array;
}
