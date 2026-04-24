<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\Service\TwoPercentConsumptionTaxCalculator;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(TwoPercentConsumptionTaxCalculator::class)]
final class TwoPercentConsumptionTaxCalculatorTest extends TestCase
{
    public function testNetPayableIsTwentyPercentOfOutputTax(): void
    {
        $calc = new TwoPercentConsumptionTaxCalculator();
        $s = $calc->calculate($this->period(), [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
        ]);
        self::assertSame('100000.0000', $s->outputTax);
        self::assertSame('80000.0000', $s->deductibleInputTax);
        // Net = 100_000 × 20% = 20_000
        self::assertSame('20000.0000', $s->netTaxPayable);
    }

    public function testMixedRatesStillStackAtTwentyPercent(): void
    {
        $calc = new TwoPercentConsumptionTaxCalculator();
        $s = $calc->calculate($this->period(), [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '800000.0000',
                taxAmount: '80000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-02'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '8.00',
                isReduced: true,
                amountExcludingTax: '200000.0000',
                taxAmount: '16000.0000',
            ),
        ]);
        // output tax = 96000; net = 96000 × 20% = 19200
        self::assertSame('96000.0000', $s->outputTax);
        self::assertSame('19200.0000', $s->netTaxPayable);
    }

    public function testIgnoresPurchaseTransactions(): void
    {
        $calc = new TwoPercentConsumptionTaxCalculator();
        $s = $calc->calculate($this->period(), [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-02'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '500000.0000',
                taxAmount: '50000.0000',
            ),
        ]);
        self::assertSame('20000.0000', $s->netTaxPayable);
        // purchaseByRate is empty under two-percent.
        self::assertSame([], $s->purchasesByRate);
    }

    public function testRejectsNonTwoPercentPeriod(): void
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $period = new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
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
        $this->expectException(ValidationException::class);
        (new TwoPercentConsumptionTaxCalculator())->calculate($period, []);
    }

    private function period(): ConsumptionTaxPeriod
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFrom: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            periodTo: new DateTimeImmutable('2027-03-31T00:00:00Z'),
            calculationMethod: ConsumptionTaxCalculationMethod::TwoPercent,
            simplifiedBusinessCategory: null,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
