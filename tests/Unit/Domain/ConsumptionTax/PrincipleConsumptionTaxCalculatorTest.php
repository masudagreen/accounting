<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\ConsumptionTax\Service\InvoiceDeductionCalculator;
use Rucaro\Domain\ConsumptionTax\Service\PrincipleConsumptionTaxCalculator;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(PrincipleConsumptionTaxCalculator::class)]
#[CoversClass(ConsumptionTaxSettlement::class)]
#[CoversClass(TaxableTransaction::class)]
final class PrincipleConsumptionTaxCalculatorTest extends TestCase
{
    public function testBasicStandardRatePrincipleNetsOutputAgainstInput(): void
    {
        $period = $this->period();
        $calc = new PrincipleConsumptionTaxCalculator();

        $txs = [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-05'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '500000.0000',
                taxAmount: '50000.0000',
            ),
        ];
        $s = $calc->calculate($period, $txs);

        self::assertSame('1000000.0000', $s->taxableSales);
        self::assertSame('100000.0000', $s->outputTax);
        self::assertSame('50000.0000', $s->deductibleInputTax);
        self::assertSame('50000.0000', $s->netTaxPayable);
        self::assertSame('0.0000', $s->adjustmentForNonRegistered);
        self::assertSame('100000.0000', $s->outputTaxByRate['standard_10']);
    }

    public function testMixedStandardAndReducedRateBreakdown(): void
    {
        $period = $this->period();
        $calc = new PrincipleConsumptionTaxCalculator();
        $txs = [
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
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-05'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '400000.0000',
                taxAmount: '40000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-10-06'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '8.00',
                isReduced: true,
                amountExcludingTax: '100000.0000',
                taxAmount: '8000.0000',
            ),
        ];
        $s = $calc->calculate($period, $txs);

        self::assertSame('1000000.0000', $s->taxableSales);
        self::assertSame('96000.0000', $s->outputTax);
        self::assertSame('48000.0000', $s->deductibleInputTax);
        self::assertSame('48000.0000', $s->netTaxPayable);

        self::assertSame('80000.0000', $s->outputTaxByRate['standard_10']);
        self::assertSame('16000.0000', $s->outputTaxByRate['reduced_8']);
        self::assertSame('40000.0000', $s->inputTaxByRate['standard_10']);
        self::assertSame('8000.0000', $s->inputTaxByRate['reduced_8']);
    }

    public function testInvoiceTransitionMeasureLimitsDeductionToEightyPercent(): void
    {
        $period = $this->period();
        $calc = new PrincipleConsumptionTaxCalculator(new InvoiceDeductionCalculator());
        $txs = [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
            // non-registered counter-party, 80% transitional measure active.
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-05'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchaseNonRegistered,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '100000.0000',
                taxAmount: '10000.0000',
            ),
        ];
        $s = $calc->calculate($period, $txs);

        self::assertSame('100000.0000', $s->outputTax);
        // 80% of 10_000 = 8_000 deductible
        self::assertSame('8000.0000', $s->deductibleInputTax);
        // 20% adjustment
        self::assertSame('2000.0000', $s->adjustmentForNonRegistered);
        // Net = 100,000 - 8,000 = 92,000
        self::assertSame('92000.0000', $s->netTaxPayable);
    }

    public function testNonTaxableAndExemptSalesSurfaceInBreakdown(): void
    {
        $period = $this->period();
        $calc = new PrincipleConsumptionTaxCalculator();
        $txs = [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-05'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '800000.0000',
                taxAmount: '80000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-06'),
                categoryCode: ConsumptionTaxCategoryCode::NonTaxableSales,
                ratePercent: '0.00',
                isReduced: false,
                amountExcludingTax: '100000.0000',
                taxAmount: '0.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-07'),
                categoryCode: ConsumptionTaxCategoryCode::ExemptSales,
                ratePercent: '0.00',
                isReduced: false,
                amountExcludingTax: '50000.0000',
                taxAmount: '0.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-07'),
                categoryCode: ConsumptionTaxCategoryCode::UntaxedSales,
                ratePercent: '0.00',
                isReduced: false,
                amountExcludingTax: '25000.0000',
                taxAmount: '0.0000',
            ),
        ];
        $s = $calc->calculate($period, $txs);
        self::assertSame('800000.0000', $s->taxableSales);
        self::assertSame('100000.0000', $s->nonTaxableSales);
        self::assertSame('50000.0000', $s->exemptSales);
        self::assertSame('25000.0000', $s->untaxedSales);
        // totalSales = taxable + nonTaxable + exempt (excludes untaxed)
        self::assertSame('950000.0000', $s->totalSales);
        // numerator = taxable + exempt = 850_000 ; den = 950_000 ; ratio ≈ 0.8947
        // bcmath 非依存の比較（float cast + 許容誤差 1e-4）
        self::assertGreaterThanOrEqual(
            0.8947 - 1e-4,
            (float) $s->taxableSalesRatio,
            'ratio should be at least 0.8947',
        );
    }

    public function testSkipsTransactionsOutsidePeriod(): void
    {
        $period = $this->period();
        $calc = new PrincipleConsumptionTaxCalculator();
        $txs = [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2025-01-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '5000000.0000',
                taxAmount: '500000.0000',
            ),
        ];
        $s = $calc->calculate($period, $txs);
        self::assertSame('0.0000', $s->outputTax);
        self::assertSame('0.0000', $s->taxableSales);
    }

    public function testRejectsNonPrinciplePeriod(): void
    {
        $period = new ConsumptionTaxPeriod(
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
            createdAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
        );
        $this->expectException(ValidationException::class);
        (new PrincipleConsumptionTaxCalculator())->calculate($period, []);
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
