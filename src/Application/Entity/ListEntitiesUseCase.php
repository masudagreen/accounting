<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;

/**
 * List the authenticated user's entities with pagination and filters.
 */
final readonly class ListEntitiesUseCase
{
    public function __construct(
        private EntityRepositoryInterface $entities,
    ) {
    }

    public function execute(ListEntitiesUseCaseInput $input): ListEntitiesUseCaseOutput
    {
        /** @var list<Entity> $items */
        $items = $this->entities->listByOwner(
            $input->ownerUserId,
            $input->page,
            $input->pageSize,
            $input->search,
            $input->isActive,
        );
        $total = $this->entities->countByOwner(
            $input->ownerUserId,
            $input->search,
            $input->isActive,
        );
        return new ListEntitiesUseCaseOutput(
            items: $items,
            total: $total,
            page: $input->page,
            pageSize: $input->pageSize,
        );
    }
}
