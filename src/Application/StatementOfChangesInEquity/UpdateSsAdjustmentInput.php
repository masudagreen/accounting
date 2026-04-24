<?php

declare(strict_types=1);

namespace Rucaro\Application\StatementOfChangesInEquity;

use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;

/**
 * PATCH-style input — any `null` field is preserved from the existing
 * row rather than forced to NULL, mirroring the Budget update pattern.
 */
final readonly class UpdateSsAdjustmentInput
{
    public function __construct(
        public string $id,
        public ?SsSectionCode $sectionCode = null,
        public ?SsChangeType $changeType = null,
        public ?string $amount = null,
        public ?string $label = null,
        public ?int $sortOrder = null,
        public ?string $notes = null,
    ) {
    }
}
