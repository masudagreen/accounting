<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\MoneyAmount;

#[CoversClass(MoneyAmount::class)]
final class MoneyAmountTest extends TestCase
{
    #[DataProvider('normalisationCases')]
    public function testNormalisesToScale4(string $input, string $expected): void
    {
        $m = new MoneyAmount($input);
        self::assertSame($expected, $m->toPrimitive());
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function normalisationCases(): array
    {
        return [
            ['0',         '0.0000'],
            ['1',         '1.0000'],
            ['100',       '100.0000'],
            ['100.5',     '100.5000'],
            ['100.1234',  '100.1234'],
        ];
    }

    public function testZeroFactoryReturnsZero(): void
    {
        self::assertTrue(MoneyAmount::zero()->isZero());
    }

    public function testPlusProducesSum(): void
    {
        $a = new MoneyAmount('100.0000');
        $b = new MoneyAmount('50.2500');
        self::assertSame('150.2500', $a->plus($b)->toPrimitive());
    }

    public function testMinusProducesDifference(): void
    {
        $a = new MoneyAmount('100.0000');
        $b = new MoneyAmount('50.2500');
        self::assertSame('49.7500', $a->minus($b)->toPrimitive());
    }

    public function testMinusRejectsUnderflow(): void
    {
        $this->expectException(ValidationException::class);
        (new MoneyAmount('10.0000'))->minus(new MoneyAmount('20.0000'));
    }

    public function testNegativeInputIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        new MoneyAmount('-1.0000');
    }

    #[DataProvider('invalidInputs')]
    public function testInvalidFormatIsRejected(string $bad): void
    {
        $this->expectException(ValidationException::class);
        new MoneyAmount($bad);
    }

    /**
     * @return list<array{0: string}>
     */
    public static function invalidInputs(): array
    {
        return [
            ['abc'],
            [''],
            ['1.23456'], // more than 4 fractional digits
            ['1e10'],
        ];
    }

    public function testEqualsByValue(): void
    {
        self::assertTrue((new MoneyAmount('100.0000'))->equals(new MoneyAmount('100.0000')));
        self::assertFalse((new MoneyAmount('100.0000'))->equals(new MoneyAmount('200.0000')));
    }

    public function testIsGreaterThanOrEqual(): void
    {
        $a = new MoneyAmount('100.0000');
        $b = new MoneyAmount('50.0000');
        self::assertTrue($a->isGreaterThanOrEqual($b));
        self::assertFalse($b->isGreaterThanOrEqual($a));
        self::assertTrue($a->isGreaterThanOrEqual($a));
    }
}
