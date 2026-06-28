<?php

declare(strict_types=1);

namespace Rucaro\Application\TrialBalance;

final readonly class RefreshTrialBalanceSnapshotUseCaseInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public \DateTimeImmutable $monthStartDate,
        public \DateTimeImmutable $monthEndDate,
    ) {
    }
}
