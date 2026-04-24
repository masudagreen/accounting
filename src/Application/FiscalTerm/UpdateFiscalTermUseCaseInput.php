<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

final readonly class UpdateFiscalTermUseCaseInput
{
    public function __construct(
        public string $id,
        public int $fiscalPeriod,
        public string $startDate,
        public string $endDate,
        public bool $isClosed,
    ) {
    }
}
