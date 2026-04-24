<?php

declare(strict_types=1);

namespace Rucaro\Application\TrialBalance;

use DateTimeImmutable;

/**
 * Input for {@see QueryTrialBalanceUseCase}.
 *
 * `asOf` defines the upper bound (inclusive). The lower bound is always the
 * start of the fiscal term, expressed as `fiscalTermStartDate`. Both dates
 * flow in as {@see DateTimeImmutable} so callers don't have to worry about
 * format drift between SQL DATE columns and ISO-8601 strings.
 */
final readonly class QueryTrialBalanceUseCaseInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fiscalTermStartDate,
        public DateTimeImmutable $asOf,
        public string $currencyCode = 'JPY',
    ) {
    }
}
