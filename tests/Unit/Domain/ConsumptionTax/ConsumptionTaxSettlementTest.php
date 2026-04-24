<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;

#[CoversClass(ConsumptionTaxSettlement::class)]
final class ConsumptionTaxSettlementTest extends TestCase
{
    public function testTaxSplitNationalLocalForStandardTenPercent(): void
    {
        $s = new ConsumptionTaxSettlement(
            period: $this->period(),
            method: ConsumptionTaxCalculationMethod::Principle,
            taxableSales: '1000000.0000',
            nonTaxableSales: '0.0000',
            exemptSales: '0.0000',
            untaxedSales: '0.0000',
            totalSales: '1000000.0000',
            taxableSalesRatio: '1.0000',
            outputTax: '100000.0000',
            deductibleInputTax: '0.0000',
            adjustmentForNonRegistered: '0.0000',
            netTaxPayable: '100000.0000',
            salesByRate: ['standard_10' => '1000000.0000'],
            outputTaxByRate: ['standard_10' => '100000.0000'],
            purchasesByRate: [],
            inputTaxByRate: [],
        );
        $split = $s->taxSplitNationalLocal();
        // 10% standard: national 78% + local 22% of the tax amount.
        // 100_000 × 78/100 = 78_000, local = 22_000
        self::assertSame('78000.0000', $split['national']);
        self::assertSame('22000.0000', $split['local']);
    }

    public function testTaxSplitInputTaxSubtracts(): void
    {
        $s = new ConsumptionTaxSettlement(
            period: $this->period(),
            method: ConsumptionTaxCalculationMethod::Principle,
            taxableSales: '1000000.0000',
            nonTaxableSales: '0.0000',
            exemptSales: '0.0000',
            untaxedSales: '0.0000',
            totalSales: '1000000.0000',
            taxableSalesRatio: '1.0000',
            outputTax: '100000.0000',
            deductibleInputTax: '50000.0000',
            adjustmentForNonRegistered: '0.0000',
            netTaxPayable: '50000.0000',
            salesByRate: ['standard_10' => '1000000.0000'],
            outputTaxByRate: ['standard_10' => '100000.0000'],
            purchasesByRate: ['standard_10' => '500000.0000'],
            inputTaxByRate: ['standard_10' => '50000.0000'],
        );
        $split = $s->taxSplitNationalLocal();
        // output national 78_000 − input national 39_000 = 39_000
        self::assertSame('39000.0000', $split['national']);
        self::assertSame('11000.0000', $split['local']);
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
