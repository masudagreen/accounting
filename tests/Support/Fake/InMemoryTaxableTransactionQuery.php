<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\ConsumptionTax\TaxableTransactionQueryInterface;

final class InMemoryTaxableTransactionQuery implements TaxableTransactionQueryInterface
{
    /**
     * @param list<TaxableTransaction> $transactions
     */
    public function __construct(private array $transactions = [])
    {
    }

    /**
     * @param list<TaxableTransaction> $transactions
     */
    public function setTransactions(array $transactions): void
    {
        $this->transactions = $transactions;
    }

    public function findByPeriod(string $entityId, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        /** @var list<TaxableTransaction> $out */
        $out = [];
        foreach ($this->transactions as $t) {
            if ($t->bookedOn >= $from && $t->bookedOn <= $to) {
                $out[] = $t;
            }
        }
        return $out;
    }
}
