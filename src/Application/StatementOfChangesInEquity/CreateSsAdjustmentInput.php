<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;

final readonly class CreateSsAdjustmentInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public SsSectionCode $sectionCode,
        public SsChangeType $changeType,
        public string $amount,
        public string $label,
        public int $sortOrder,
        public ?string $notes,
    ) {
    }
}
