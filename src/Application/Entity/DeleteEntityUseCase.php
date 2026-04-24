<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Support\Clock\ClockInterface;

final readonly class DeleteEntityUseCase
{
    public function __construct(
        private EntityRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw EntityNotFoundException::for('Entity', $id);
        }
        $this->repo->softDelete($id, $this->clock->getCurrentTime());
    }
}
