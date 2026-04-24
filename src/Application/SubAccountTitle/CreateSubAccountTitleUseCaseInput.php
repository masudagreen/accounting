<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

final readonly class CreateSubAccountTitleUseCaseInput
{
    public function __construct(
        public string $accountTitleId,
        public string $code,
        public string $name,
        public int $sortOrder,
        public bool $isActive,
    ) {
    }
}
