<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;

/**
 * Thin adapter that calls {@see CalculateConsumptionTaxUseCase} and
 * returns the settlement aggregate unchanged. Exists so HTTP
 * controllers have a stable dispatch target even when the calculation
 * is later split across multiple services.
 */
final readonly class GenerateConsumptionTaxReportUseCase
{
    public function __construct(
        private CalculateConsumptionTaxUseCase $calculate,
    ) {
    }

    public function execute(string $periodId): ConsumptionTaxSettlement
    {
        return $this->calculate->execute($periodId);
    }
}
