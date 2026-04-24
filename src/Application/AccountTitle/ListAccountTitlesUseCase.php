<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;

final readonly class ListAccountTitlesUseCase
{
    public function __construct(
        private AccountTitleRepositoryInterface $repo,
    ) {
    }

    public function execute(ListAccountTitlesUseCaseInput $input): ListAccountTitlesUseCaseOutput
    {
        /** @var list<AccountTitle> $items */
        $items = $this->repo->listByEntity(
            $input->entityId,
            $input->page,
            $input->pageSize,
            $input->category,
            $input->isActive,
            $input->search,
        );
        $total = $this->repo->countByEntity(
            $input->entityId,
            $input->category,
            $input->isActive,
            $input->search,
        );
        return new ListAccountTitlesUseCaseOutput(
            items: $items,
            total: $total,
            page: $input->page,
            pageSize: $input->pageSize,
        );
    }
}
