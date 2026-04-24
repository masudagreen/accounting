<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\Service\SimplifiedConsumptionTaxCalculator;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(SimplifiedConsumptionTaxCalculator::class)]
final class SimplifiedConsumptionTaxCalculatorTest extends TestCase
{
    public function testWholesaleNinetyPercentDeemedPurchaseRatio(): void
    {
        $period = $this->simplifiedPeriod(SimplifiedBusinessCategory::Wholesale);
        $calc = new SimplifiedConsumptionTaxCalculator();
        // 売上税 100_000 → みなし仕入 90_000 → 納付 10_000
        $s = $calc->calculate($period, [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-08-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
        ]);
        self::assertSame('100000.0000', $s->outputTax);
        self::assertSame('90000.0000', $s->deductibleInputTax);
        self::assertSame('10000.0000', $s->netTaxPayable);
    }

    public function testRetailEightyPercent(): void
    {
        $period = $this->simplifiedPeriod(SimplifiedBusinessCategory::Retail);
        $calc = new SimplifiedConsumptionTaxCalculator();
        $s = $calc->calculate($period, [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-08-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '500000.0000',
                taxAmount: '50000.0000',
            ),
        ]);
        self::assertSame('50000.0000', $s->outputTax);
        self::assertSame('40000.0000', $s->deductibleInputTax);
        self::assertSame('10000.0000', $s->netTaxPayable);
    }

    public function testPurchaseAmountsDoNotAffectPayable(): void
    {
        $period = $this->simplifiedPeriod(SimplifiedBusinessCategory::Service);
        $calc = new SimplifiedConsumptionTaxCalculator();
        // Service (5種) = 50%. Purchases should not move the needle.
        $s = $calc->calculate($period, [
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-08-01'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1000000.0000',
                taxAmount: '100000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-08-02'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '999999.0000',
                taxAmount: '99999.9000',
            ),
        ]);
        self::assertSame('50000.0000', $s->deductibleInputTax);
        self::assertSame('50000.0000', $s->netTaxPayable);
    }

    public function testRejectsNonSimplifiedPeriod(): void
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
        (new SimplifiedConsumptionTaxCalculator())->calculate($period, []);
    }

    private function simplifiedPeriod(SimplifiedBusinessCategory $sbc): ConsumptionTaxPeriod
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFrom: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            periodTo: new DateTimeImmutable('2027-03-31T00:00:00Z'),
            calculationMethod: ConsumptionTaxCalculationMethod::Simplified,
            simplifiedBusinessCategory: $sbc,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
