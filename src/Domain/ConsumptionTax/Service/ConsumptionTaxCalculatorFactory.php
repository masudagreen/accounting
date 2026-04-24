<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax\Service;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;

/**
 * Builds a calculator for the method carried on a period.
 */
final class ConsumptionTaxCalculatorFactory
{
    public function __construct(
        private readonly InvoiceDeductionCalculator $invoiceDeduction = new InvoiceDeductionCalculator(),
    ) {
    }

    public function forMethod(ConsumptionTaxCalculationMethod $method): ConsumptionTaxCalculatorInterface
    {
        return match ($method) {
            ConsumptionTaxCalculationMethod::Principle  => new PrincipleConsumptionTaxCalculator($this->invoiceDeduction),
            ConsumptionTaxCalculationMethod::Simplified => new SimplifiedConsumptionTaxCalculator(),
            ConsumptionTaxCalculationMethod::TwoPercent => new TwoPercentConsumptionTaxCalculator(),
        };
    }
}
