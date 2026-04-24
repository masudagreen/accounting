<?php

declare(strict_types=1);

namespace Rucaro\Application\Journal;

use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;

final readonly class ListJournalsUseCase
{
    public function __construct(
        private JournalRepositoryInterface $journals,
    ) {
    }

    public function execute(ListJournalsUseCaseInput $input): ListJournalsUseCaseOutput
    {
        /** @var list<Journal> $items */
        $items = $this->journals->searchByEntity(
            $input->entityId,
            $input->page,
            $input->pageSize,
            $input->fiscalTermId,
            $input->from,
            $input->to,
            $input->status,
            $input->source,
            $input->search,
            $input->includeTrashed,
        );
        $total = $this->journals->countByEntity(
            $input->entityId,
            $input->fiscalTermId,
            $input->from,
            $input->to,
            $input->status,
            $input->source,
            $input->search,
            $input->includeTrashed,
        );
        return new ListJournalsUseCaseOutput(
            items: $items,
            total: $total,
            page: $input->page,
            pageSize: $input->pageSize,
        );
    }
}
