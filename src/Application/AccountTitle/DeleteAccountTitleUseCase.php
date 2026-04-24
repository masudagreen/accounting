<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Soft-delete an {@see \Rucaro\Domain\AccountTitle\AccountTitle}.
 *
 * Referential integrity against journals is left to the database's ON DELETE
 * RESTRICT — if journals reference this title, the DB will raise and the
 * controller reports the error in flash.
 */
final readonly class DeleteAccountTitleUseCase
{
    public function __construct(
        private AccountTitleRepositoryInterface $repo,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw EntityNotFoundException::for('AccountTitle', $id);
        }
        $this->repo->softDelete($id, $this->clock->getCurrentTime());
    }
}
