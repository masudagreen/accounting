<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;

final readonly class GetBlueReturnUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
    ) {
    }

    public function execute(string $id): ?BlueReturnForm
    {
        return $this->forms->findById($id);
    }
}
