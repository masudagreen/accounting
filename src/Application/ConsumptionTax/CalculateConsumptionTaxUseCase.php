<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\ConsumptionTax\Service\ConsumptionTaxCalculatorFactory;
use Rucaro\Domain\ConsumptionTax\TaxableTransactionQueryInterface;
use Rucaro\Domain\Exception\EntityNotFoundException;

/**
 * Aggregate journal lines over a {@see ConsumptionTaxPeriod} and run the
 * appropriate calculator.
 */
final readonly class CalculateConsumptionTaxUseCase
{
    public function __construct(
        private ConsumptionTaxPeriodRepositoryInterface $periods,
        private TaxableTransactionQueryInterface $transactions,
        private ConsumptionTaxCalculatorFactory $factory,
    ) {
    }

    public function execute(string $periodId): ConsumptionTaxSettlement
    {
        $period = $this->periods->findById($periodId);
        if ($period === null) {
            throw EntityNotFoundException::for('ConsumptionTaxPeriod', $periodId);
        }
        $txs = $this->transactions->findByPeriod(
            $period->entityId,
            $period->periodFrom,
            $period->periodTo,
        );
        $calc = $this->factory->forMethod($period->calculationMethod);
        return $calc->calculate($period, $txs);
    }
}
