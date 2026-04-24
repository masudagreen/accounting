<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\Service\BlueReturnBuilder;

/**
 * Thin wrapper over {@see BlueReturnBuilder}. Kept as a use case so the
 * HTTP layer can depend on the application namespace instead of reaching
 * into domain services directly.
 */
final readonly class GenerateBlueReturnSnapshotUseCase
{
    public function __construct(
        private BlueReturnBuilder $builder = new BlueReturnBuilder(),
    ) {
    }

    public function execute(GenerateBlueReturnSnapshotInput $input): BlueReturnSnapshot
    {
        return $this->builder->build(
            $input->formType,
            $input->revenueByAccount,
            $input->costOfSalesByAccount,
            $input->expensesByAccount,
            $input->monthlyRows,
            $input->breakdown,
            $input->assetsByAccount,
            $input->liabilitiesByAccount,
            $input->equityByAccount,
        );
    }
}
