<?php

declare(strict_types=1);

namespace Rucaro\Application\BreakEvenPoint;

use DateTimeImmutable;

final readonly class AnalyzeBreakEvenPointInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode = 'JPY',
    ) {
    }
}
