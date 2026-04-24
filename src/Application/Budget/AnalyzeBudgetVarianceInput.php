<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use DateTimeImmutable;

/**
 * Input for {@see AnalyzeBudgetVarianceUseCase}.
 *
 *  - `fiscalTermStartDate` is the lower bound of the actual roll-up
 *    (typically the fiscal term's `start_date`).
 *  - `asOf` is the upper bound; defaults to the clock's "now" when the
 *    HTTP adapter omits it.
 */
final readonly class AnalyzeBudgetVarianceInput
{
    public function __construct(
        public string $budgetId,
        public DateTimeImmutable $fiscalTermStartDate,
        public DateTimeImmutable $asOf,
        public string $currencyCode = 'JPY',
    ) {
    }
}
