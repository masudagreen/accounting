<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;

final readonly class ListBlueReturnsUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
    ) {
    }

    /**
     * @return list<BlueReturnForm>
     */
    public function execute(string $entityId, ?string $fiscalTermId = null): array
    {
        return $this->forms->findByEntity($entityId, $fiscalTermId, false);
    }
}
