<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\Service\ConsumptionTaxCalculatorFactory;
use Rucaro\Domain\ConsumptionTax\Service\PrincipleConsumptionTaxCalculator;
use Rucaro\Domain\ConsumptionTax\Service\SimplifiedConsumptionTaxCalculator;
use Rucaro\Domain\ConsumptionTax\Service\TwoPercentConsumptionTaxCalculator;

#[CoversClass(ConsumptionTaxCalculatorFactory::class)]
final class ConsumptionTaxCalculatorFactoryTest extends TestCase
{
    public function testReturnsPrincipleCalculator(): void
    {
        $f = new ConsumptionTaxCalculatorFactory();
        self::assertInstanceOf(
            PrincipleConsumptionTaxCalculator::class,
            $f->forMethod(ConsumptionTaxCalculationMethod::Principle),
        );
    }

    public function testReturnsSimplifiedCalculator(): void
    {
        $f = new ConsumptionTaxCalculatorFactory();
        self::assertInstanceOf(
            SimplifiedConsumptionTaxCalculator::class,
            $f->forMethod(ConsumptionTaxCalculationMethod::Simplified),
        );
    }

    public function testReturnsTwoPercentCalculator(): void
    {
        $f = new ConsumptionTaxCalculatorFactory();
        self::assertInstanceOf(
            TwoPercentConsumptionTaxCalculator::class,
            $f->forMethod(ConsumptionTaxCalculationMethod::TwoPercent),
        );
    }
}
