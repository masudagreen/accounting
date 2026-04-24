<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Multi;

use DateTimeImmutable;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;

/**
 * Input DTO for {@see GenerateMultiPeriodFinancialStatementUseCase}.
 *
 * `fiscalTermIds` is the raw (possibly unsorted) list the HTTP controller
 * received; the use case re-orders it internally by `start_date` ascending so
 * the rightmost column in the output is always the most recent period.
 *
 * `asOf` is optional — when null the generator will default to the end date
 * of the most recent term, matching legacy `FinancialStatementMulti`'s
 * "close-of-period" semantic.
 */
final readonly class GenerateMultiPeriodFinancialStatementInput
{
    public const MAX_PERIODS = 5;

    /**
     * @param list<string> $fiscalTermIds
     */
    public function __construct(
        public string $entityId,
        public array $fiscalTermIds,
        public FinancialStatementKind $kind,
        public ?DateTimeImmutable $asOf = null,
        public string $currencyCode = 'JPY',
    ) {
    }
}
