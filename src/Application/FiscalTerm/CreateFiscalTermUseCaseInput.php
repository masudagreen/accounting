<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

final readonly class CreateFiscalTermUseCaseInput
{
    public function __construct(
        public string $entityId,
        public int $fiscalPeriod,
        public string $startDate,
        public string $endDate,
        public bool $isClosed,
    ) {
    }
}
