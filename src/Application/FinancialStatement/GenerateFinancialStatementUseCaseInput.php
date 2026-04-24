<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement;

use DateTimeImmutable;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;

/**
 * Input for {@see GenerateFinancialStatementUseCase}.
 *
 * `fromDate` defaults to the fiscal term start when the caller is the HTTP
 * controller; `asOf` defaults to today. Both must be passed explicitly here
 * so the use case stays deterministic.
 */
final readonly class GenerateFinancialStatementUseCaseInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public FinancialStatementKind $kind,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $asOf,
        public string $currencyCode = 'JPY',
    ) {
    }
}
