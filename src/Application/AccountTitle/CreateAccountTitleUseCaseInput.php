<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

final readonly class CreateAccountTitleUseCaseInput
{
    public function __construct(
        public string $entityId,
        public string $code,
        public string $name,
        public string $category,
        public string $normalSide,
        public ?string $parentId,
        public int $sortOrder,
        public bool $isActive,
    ) {
    }
}
