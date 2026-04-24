<?php

declare(strict_types=1);

namespace Rucaro\Application\FiscalTerm;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;

final readonly class DeleteFiscalTermUseCase
{
    public function __construct(
        private FiscalTermRepositoryInterface $repo,
    ) {
    }

    public function execute(string $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw EntityNotFoundException::for('FiscalTerm', $id);
        }
        $this->repo->delete($id);
    }
}
