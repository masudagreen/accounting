<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(ConsumptionTaxPeriod::class)]
#[CoversClass(ConsumptionTaxCalculationMethod::class)]
#[CoversClass(SimplifiedBusinessCategory::class)]
final class ConsumptionTaxPeriodTest extends TestCase
{
    public function testPrinciplePeriodAcceptsNoBusinessCategory(): void
    {
        $p = $this->build(ConsumptionTaxCalculationMethod::Principle, null);
        self::assertNull($p->simplifiedBusinessCategory);
    }

    public function testSimplifiedPeriodRequiresBusinessCategory(): void
    {
        $this->expectException(ValidationException::class);
        $this->build(ConsumptionTaxCalculationMethod::Simplified, null);
    }

    public function testPrinciplePeriodRejectsBusinessCategory(): void
    {
        $this->expectException(ValidationException::class);
        $this->build(ConsumptionTaxCalculationMethod::Principle, SimplifiedBusinessCategory::Wholesale);
    }

    public function testContainsChecksRange(): void
    {
        $p = $this->build();
        self::assertTrue($p->contains(new DateTimeImmutable('2026-10-01T00:00:00Z')));
        self::assertFalse($p->contains(new DateTimeImmutable('2025-01-01T00:00:00Z')));
        self::assertFalse($p->contains(new DateTimeImmutable('2028-01-01T00:00:00Z')));
    }

    public function testRejectsInvertedPeriod(): void
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $this->expectException(ValidationException::class);
        new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFrom: new DateTimeImmutable('2027-03-31T00:00:00Z'),
            periodTo: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            calculationMethod: ConsumptionTaxCalculationMethod::Principle,
            simplifiedBusinessCategory: null,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function testDeemedPurchaseRatiosMatchLawTable(): void
    {
        self::assertSame('90', SimplifiedBusinessCategory::Wholesale->deemedPurchaseRatio());
        self::assertSame('80', SimplifiedBusinessCategory::Retail->deemedPurchaseRatio());
        self::assertSame('70', SimplifiedBusinessCategory::Manufacturing->deemedPurchaseRatio());
        self::assertSame('60', SimplifiedBusinessCategory::Other->deemedPurchaseRatio());
        self::assertSame('50', SimplifiedBusinessCategory::Service->deemedPurchaseRatio());
        self::assertSame('40', SimplifiedBusinessCategory::RealEstate->deemedPurchaseRatio());
    }

    public function testWithStatusReturnsNewInstance(): void
    {
        $p = $this->build();
        $now = new DateTimeImmutable('2027-05-01T00:00:00Z');
        $updated = $p->withStatus('filed', $now, $now);
        self::assertSame('filed', $updated->settlementStatus);
        self::assertNotSame($p, $updated);
        self::assertSame('pending', $p->settlementStatus);
    }

    private function build(
        ConsumptionTaxCalculationMethod $method = ConsumptionTaxCalculationMethod::Principle,
        ?SimplifiedBusinessCategory $sbc = null,
    ): ConsumptionTaxPeriod {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            periodFrom: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            periodTo: new DateTimeImmutable('2027-03-31T00:00:00Z'),
            calculationMethod: $method,
            simplifiedBusinessCategory: $sbc,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
