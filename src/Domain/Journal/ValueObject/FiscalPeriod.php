<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\ValueObject;

use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Support\Validation\AbstractValueObject;
use Rucaro\Support\Validation\Assert;

/**
 * Closed interval `[startDate, endDate]` that identifies a fiscal term for
 * the purposes of journal validation.
 *
 * Mirrors the `fiscal_terms` row shape from ADR-002: a ULID plus a date
 * range. Equality is purely by value so the same period coming out of two
 * different repository reads compares equal.
 */
final readonly class FiscalPeriod extends AbstractValueObject
{
    public function __construct(
        public string $fiscalTermId,
        public JournalDate $startDate,
        public JournalDate $endDate,
    ) {
        Assert::notEmpty($fiscalTermId, 'fiscalTermId');
        if ($endDate->isBefore($startDate)) {
            throw InvariantViolationException::for('fiscal_period.end_before_start', [
                'startDate' => $startDate->toPrimitive(),
                'endDate'   => $endDate->toPrimitive(),
            ]);
        }
    }

    public function contains(JournalDate $date): bool
    {
        return $date->isOnOrAfter($this->startDate) && $date->isOnOrBefore($this->endDate);
    }

    /**
     * @return array{fiscalTermId: string, startDate: string, endDate: string}
     */
    public function toPrimitive(): array
    {
        return [
            'fiscalTermId' => $this->fiscalTermId,
            'startDate'    => $this->startDate->toPrimitive(),
            'endDate'      => $this->endDate->toPrimitive(),
        ];
    }
}
