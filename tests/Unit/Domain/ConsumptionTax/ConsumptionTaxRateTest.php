<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRate;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(ConsumptionTaxRate::class)]
final class ConsumptionTaxRateTest extends TestCase
{
    public function testTaxFromBaseStandardTenPercent(): void
    {
        $rate = $this->standardTen();
        self::assertSame('100000.0000', $rate->taxFromBase('1000000'));
    }

    public function testTaxFromBaseReducedEightPercent(): void
    {
        $rate = new ConsumptionTaxRate(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAZ',
            code: 'reduced_8',
            label: '軽減 8%',
            ratePercent: '8.00',
            effectiveFrom: new DateTimeImmutable('2019-10-01'),
            effectiveUntil: null,
            isTaxable: true,
            isReduced: true,
        );
        self::assertSame('16000.0000', $rate->taxFromBase('200000'));
    }

    public function testTaxFromGrossStripsTax(): void
    {
        $rate = $this->standardTen();
        // 1,100 gross / 1.10 = 1,000 base ; tax = 100
        $tax = $rate->taxFromGross('1100');
        self::assertSame('100.0000', $tax);
    }

    public function testIsEffectiveOnRespectsWindow(): void
    {
        $rate = new ConsumptionTaxRate(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAZ',
            code: 'old_8',
            label: '旧 8%',
            ratePercent: '8.00',
            effectiveFrom: new DateTimeImmutable('2014-04-01'),
            effectiveUntil: new DateTimeImmutable('2019-09-30'),
            isTaxable: true,
            isReduced: false,
        );
        self::assertTrue($rate->isEffectiveOn(new DateTimeImmutable('2015-06-15')));
        self::assertFalse($rate->isEffectiveOn(new DateTimeImmutable('2013-04-01')));
        self::assertFalse($rate->isEffectiveOn(new DateTimeImmutable('2019-10-01')));
    }

    public function testRejectsInvalidRateString(): void
    {
        $this->expectException(ValidationException::class);
        new ConsumptionTaxRate(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAZ',
            code: 'standard_10',
            label: '標準 10%',
            ratePercent: 'abc',
            effectiveFrom: new DateTimeImmutable('2019-10-01'),
            effectiveUntil: null,
            isTaxable: true,
            isReduced: false,
        );
    }

    public function testRejectsInvertedEffectiveWindow(): void
    {
        $this->expectException(ValidationException::class);
        new ConsumptionTaxRate(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAZ',
            code: 'standard_10',
            label: '標準 10%',
            ratePercent: '10.00',
            effectiveFrom: new DateTimeImmutable('2019-10-01'),
            effectiveUntil: new DateTimeImmutable('2014-04-01'),
            isTaxable: true,
            isReduced: false,
        );
    }

    private function standardTen(): ConsumptionTaxRate
    {
        return new ConsumptionTaxRate(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAY',
            code: 'standard_10',
            label: '標準 10%',
            ratePercent: '10.00',
            effectiveFrom: new DateTimeImmutable('2019-10-01'),
            effectiveUntil: null,
            isTaxable: true,
            isReduced: false,
        );
    }
}
