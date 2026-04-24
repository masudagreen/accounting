<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

final readonly class UpdateAccountTitleUseCaseInput
{
    public function __construct(
        public string $id,
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
