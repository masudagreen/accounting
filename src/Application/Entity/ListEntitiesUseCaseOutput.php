<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

use Rucaro\Domain\Entity\Entity;

final readonly class ListEntitiesUseCaseOutput
{
    /**
     * @param list<Entity> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $pageSize,
    ) {
    }
}
