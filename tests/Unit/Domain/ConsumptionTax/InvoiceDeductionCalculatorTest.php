<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\Service\InvoiceDeductionCalculator;

#[CoversClass(InvoiceDeductionCalculator::class)]
final class InvoiceDeductionCalculatorTest extends TestCase
{
    public function testFullDeductionBeforeInvoiceRegime(): void
    {
        $c = new InvoiceDeductionCalculator();
        self::assertSame('100.00', $c->deductibleRatio(new DateTimeImmutable('2023-09-30')));
        self::assertSame('10000.0000', $c->deductibleAmount(new DateTimeImmutable('2023-09-30'), '10000'));
    }

    public function testEightyPercentWindow(): void
    {
        $c = new InvoiceDeductionCalculator();
        self::assertSame('80.00', $c->deductibleRatio(new DateTimeImmutable('2024-01-15')));
        self::assertSame('80.00', $c->deductibleRatio(new DateTimeImmutable('2026-09-30')));
        self::assertSame('8000.0000', $c->deductibleAmount(new DateTimeImmutable('2024-01-15'), '10000'));
        self::assertSame('2000.0000', $c->disallowedAmount(new DateTimeImmutable('2024-01-15'), '10000'));
    }

    public function testFiftyPercentWindow(): void
    {
        $c = new InvoiceDeductionCalculator();
        self::assertSame('50.00', $c->deductibleRatio(new DateTimeImmutable('2026-10-01')));
        self::assertSame('50.00', $c->deductibleRatio(new DateTimeImmutable('2029-09-30')));
        self::assertSame('5000.0000', $c->deductibleAmount(new DateTimeImmutable('2027-06-01'), '10000'));
    }

    public function testZeroDeductionAfterSunset(): void
    {
        $c = new InvoiceDeductionCalculator();
        self::assertSame('0.00', $c->deductibleRatio(new DateTimeImmutable('2029-10-01')));
        self::assertSame('0.0000', $c->deductibleAmount(new DateTimeImmutable('2030-01-15'), '10000'));
        self::assertSame('10000.0000', $c->disallowedAmount(new DateTimeImmutable('2030-01-15'), '10000'));
    }
}
