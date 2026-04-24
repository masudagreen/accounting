<?php

declare(strict_types=1);

namespace Rucaro\Domain\TrialBalance;

use DateTimeImmutable;
use Rucaro\Support\Decimal\Decimal;

/**
 * Read model for a trial balance over a given period.
 *
 * Projects {@see \Rucaro\Domain\Journal\Journal} postings grouped by
 * account title. Nothing in the domain mutates this object; it is built
 * once by the query service (or composed from a monthly snapshot plus live
 * journal tail) and handed to the HTTP layer unchanged.
 */
final readonly class TrialBalance
{
    /**
     * @param list<TrialBalanceRow> $rows
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode,
        public array $rows,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    /**
     * Debit total across all rows (normalised scale-4).
     */
    public function debitTotal(): string
    {
        $sum = '0.0000';
        foreach ($this->rows as $row) {
            $sum = Decimal::add($sum, $row->debitTotal);
        }
        return Decimal::normalize($sum);
    }

    /**
     * Credit total across all rows (normalised scale-4).
     */
    public function creditTotal(): string
    {
        $sum = '0.0000';
        foreach ($this->rows as $row) {
            $sum = Decimal::add($sum, $row->creditTotal);
        }
        return Decimal::normalize($sum);
    }

    /**
     * True when debit total equals credit total at scale 4.
     */
    public function isBalanced(): bool
    {
        return Decimal::compare($this->debitTotal(), $this->creditTotal()) === 0;
    }
}
