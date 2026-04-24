<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

final readonly class ListAccountTitlesUseCaseInput
{
    public function __construct(
        public string $entityId,
        /** @var int<1, max> */
        public int $page,
        /** @var int<1, max> */
        public int $pageSize,
        public ?string $category = null,
        public ?bool $isActive = null,
        public ?string $search = null,
    ) {
    }
}
