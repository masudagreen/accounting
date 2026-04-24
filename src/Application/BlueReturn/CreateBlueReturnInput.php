<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnFormType;

final readonly class CreateBlueReturnInput
{
    /**
     * @param array<string, mixed> $snapshot raw 4-page payload; may be empty
     *                                       to start from an empty skeleton.
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public BlueReturnFormType $formType,
        public array $snapshot,
        public string $createdBy,
    ) {
    }
}
