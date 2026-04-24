<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use DateTimeImmutable;

/**
 * Input envelope for {@see GenerateStatementOfChangesInEquityUseCase}.
 *
 * `openingBalances` is a `section_code => decimal string` map (any
 * missing column is treated as zero). `netIncome` is the period net
 * profit sourced upstream — the UseCase folds it into the
 * RetainedEarnings column as a `journal_auto` change row. Passing
 * `null` skips the fold, which the UI uses when the PL is not yet
 * closed for the period.
 */
final readonly class GenerateStatementOfChangesInEquityInput
{
    /**
     * @param array<string, string> $openingBalances
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode = 'JPY',
        public array $openingBalances = [],
        public ?string $netIncome = null,
    ) {
    }
}
