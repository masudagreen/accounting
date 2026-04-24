<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnFormType;

final readonly class UpdateBlueReturnInput
{
    /**
     * @param array<string, mixed>|null $snapshot null = keep current snapshot.
     */
    public function __construct(
        public string $id,
        public ?BlueReturnFormType $formType,
        public ?array $snapshot,
    ) {
    }
}
