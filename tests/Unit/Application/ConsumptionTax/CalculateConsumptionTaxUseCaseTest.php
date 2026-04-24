<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\ConsumptionTax\CalculateConsumptionTaxUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\Service\ConsumptionTaxCalculatorFactory;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Tests\Support\Fake\InMemoryConsumptionTaxPeriodRepository;
use Rucaro\Tests\Support\Fake\InMemoryTaxableTransactionQuery;

#[CoversClass(CalculateConsumptionTaxUseCase::class)]
final class CalculateConsumptionTaxUseCaseTest extends TestCase
{
    public function testProducesSettlementForRegisteredPeriod(): void
    {
        $periods = new InMemoryConsumptionTaxPeriodRepository();
        $period = $this->principlePeriod();
        $periods->save($period);

        $txs = new InMemoryTaxableTransactionQuery([
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1600000.0000',
                taxAmount: '160000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-05'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '200000.0000',
                taxAmount: '20000.0000',
            ),
        ]);
        $useCase = new CalculateConsumptionTaxUseCase($periods, $txs, new ConsumptionTaxCalculatorFactory());
        $s = $useCase->execute($period->id);

        self::assertSame('160000.0000', $s->outputTax);
        self::assertSame('20000.0000', $s->deductibleInputTax);
        self::assertSame('140000.0000', $s->netTaxPayable);
    }

    public function testMissingPeriodRaisesEntityNotFound(): void
    {
        $useCase = new CalculateConsumptionTaxUseCase(
            new InMemoryConsumptionTaxPeriodRepository(),
            new InMemoryTaxableTransactionQuery(),
            new ConsumptionTaxCalculatorFactory(),
        );
        $this->expectException(EntityNotFoundException::class);
        $useCase->execute('01HAAAAAAAAAAAAAAAAAAAAAA0');
    }

    private function principlePeriod(): ConsumptionTaxPeriod
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            periodFrom: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            periodTo: new DateTimeImmutable('2027-03-31T00:00:00Z'),
            calculationMethod: ConsumptionTaxCalculationMethod::Principle,
            simplifiedBusinessCategory: null,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
