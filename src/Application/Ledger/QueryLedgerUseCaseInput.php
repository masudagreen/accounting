<?php

declare(strict_types=1);

namespace Rucaro\Application\Ledger;

use DateTimeImmutable;

/**
 * Input for {@see QueryLedgerUseCase}.
 *
 * When {@see $fromDate} / {@see $toDate} are null the use case resolves them
 * from the fiscal term's start_date / end_date. When {@see $accountTitleId}
 * is null every account title of the entity is included.
 */
final readonly class QueryLedgerUseCaseInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public ?string $accountTitleId = null,
        public ?DateTimeImmutable $fromDate = null,
        public ?DateTimeImmutable $toDate = null,
        public string $currencyCode = 'JPY',
    ) {
    }
}
