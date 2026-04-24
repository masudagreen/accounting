<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

final readonly class DeleteSubAccountTitleUseCase
{
    public function __construct(
        private SubAccountTitleRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw EntityNotFoundException::for('SubAccountTitle', $id);
        }
        $this->repo->softDelete($id, $this->clock->getCurrentTime());
    }
}
