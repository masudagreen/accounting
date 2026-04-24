<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

final readonly class UpdateSubAccountTitleUseCaseInput
{
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public int $sortOrder,
        public bool $isActive,
    ) {
    }
}
