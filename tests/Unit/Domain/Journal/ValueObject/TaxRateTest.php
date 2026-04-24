<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\TaxRate;

#[CoversClass(TaxRate::class)]
final class TaxRateTest extends TestCase
{
    public function testStandard10Factory(): void
    {
        $r = TaxRate::standard10();
        self::assertSame('10.00', $r->toDecimal());
        self::assertFalse($r->isReduced);
    }

    public function testReduced8Factory(): void
    {
        $r = TaxRate::reduced8();
        self::assertSame('8.00', $r->toDecimal());
        self::assertTrue($r->isReduced);
    }

    public function testExemptFactory(): void
    {
        $r = TaxRate::exempt();
        self::assertSame('0.00', $r->toDecimal());
        self::assertFalse($r->isReduced);
    }

    public function testReduced8DistinguishesFromStandard8(): void
    {
        $reduced = new TaxRate('8.00', isReduced: true);
        $standard = new TaxRate('8.00', isReduced: false);
        self::assertFalse($reduced->equals($standard));
    }

    #[DataProvider('normalizationCases')]
    public function testNumericNormalisationKeepsScale2(string $input, string $expected): void
    {
        $r = new TaxRate($input);
        self::assertSame($expected, $r->toDecimal());
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function normalizationCases(): array
    {
        return [
            ['10',    '10.00'],
            ['10.0',  '10.00'],
            ['10.00', '10.00'],
            ['0',     '0.00'],
            ['8.5',   '8.50'],
        ];
    }

    #[DataProvider('invalidInputs')]
    public function testInvalidInputIsRejected(string $bad): void
    {
        $this->expectException(ValidationException::class);
        new TaxRate($bad);
    }

    /**
     * @return list<array{0: string}>
     */
    public static function invalidInputs(): array
    {
        return [
            ['abc'],
            ['10.123'], // too many fractional digits for DECIMAL(5,2)
            [''],
            ['-5.00'],
            ['-1'],
        ];
    }

    public function testOutOfRangeIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        new TaxRate('100.01');
    }

    public function testToPrimitiveTagsReducedFlag(): void
    {
        self::assertSame('R:8.00', TaxRate::reduced8()->toPrimitive());
        self::assertSame('S:10.00', TaxRate::standard10()->toPrimitive());
    }
}
