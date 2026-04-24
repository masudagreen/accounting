<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

final readonly class ListEntitiesUseCaseInput
{
    public function __construct(
        public string $ownerUserId,
        /** @var int<1, max> */
        public int $page,
        /** @var int<1, max> */
        public int $pageSize,
        public ?string $search = null,
        public ?bool $isActive = null,
    ) {
    }
}
