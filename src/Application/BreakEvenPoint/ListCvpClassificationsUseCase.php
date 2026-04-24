<?php

declare(strict_types=1);

namespace Rucaro\Application\BreakEvenPoint;

use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;

final readonly class ListCvpClassificationsUseCase
{
    public function __construct(
        private AccountTitleCvpClassificationRepositoryInterface $repo,
    ) {
    }

    /**
     * @return list<AccountTitleCvpClassification>
     */
    public function execute(string $entityId): array
    {
        return $this->repo->findAllByEntity($entityId);
    }
}
