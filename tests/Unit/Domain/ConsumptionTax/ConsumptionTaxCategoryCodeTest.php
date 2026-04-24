<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;

#[CoversClass(ConsumptionTaxCategoryCode::class)]
final class ConsumptionTaxCategoryCodeTest extends TestCase
{
    public function testSalesCategoriesReturnSalesSide(): void
    {
        self::assertSame('sales', ConsumptionTaxCategoryCode::TaxableSales->side());
        self::assertTrue(ConsumptionTaxCategoryCode::TaxableSales->isSales());
        self::assertFalse(ConsumptionTaxCategoryCode::TaxableSales->isPurchase());
    }

    public function testPurchaseCategoriesReturnPurchaseSide(): void
    {
        self::assertSame('purchase', ConsumptionTaxCategoryCode::TaxablePurchase->side());
        self::assertTrue(ConsumptionTaxCategoryCode::TaxablePurchase->isPurchase());
        self::assertFalse(ConsumptionTaxCategoryCode::TaxablePurchase->isSales());
    }

    public function testOnlyTaxablePurchasesAreDeductible(): void
    {
        self::assertTrue(ConsumptionTaxCategoryCode::TaxablePurchase->isDeductible());
        self::assertTrue(ConsumptionTaxCategoryCode::TaxablePurchaseNonRegistered->isDeductible());
        self::assertFalse(ConsumptionTaxCategoryCode::NonTaxablePurchase->isDeductible());
        self::assertFalse(ConsumptionTaxCategoryCode::TaxableSales->isDeductible());
    }

    public function testTaxableSalesAndExemptCountAsSalesNumerator(): void
    {
        self::assertTrue(ConsumptionTaxCategoryCode::TaxableSales->isInTaxableSalesNumerator());
        self::assertTrue(ConsumptionTaxCategoryCode::ExemptSales->isInTaxableSalesNumerator());
        self::assertFalse(ConsumptionTaxCategoryCode::NonTaxableSales->isInTaxableSalesNumerator());
        self::assertFalse(ConsumptionTaxCategoryCode::UntaxedSales->isInTaxableSalesNumerator());
    }

    public function testUntaxedSalesExcludedFromDenominator(): void
    {
        self::assertTrue(ConsumptionTaxCategoryCode::TaxableSales->isInTotalSalesDenominator());
        self::assertTrue(ConsumptionTaxCategoryCode::NonTaxableSales->isInTotalSalesDenominator());
        self::assertTrue(ConsumptionTaxCategoryCode::ExemptSales->isInTotalSalesDenominator());
        self::assertFalse(ConsumptionTaxCategoryCode::UntaxedSales->isInTotalSalesDenominator());
    }

    public function testIsTaxableOnlyForTaxableVariants(): void
    {
        self::assertTrue(ConsumptionTaxCategoryCode::TaxableSales->isTaxable());
        self::assertTrue(ConsumptionTaxCategoryCode::TaxablePurchase->isTaxable());
        self::assertFalse(ConsumptionTaxCategoryCode::NonTaxableSales->isTaxable());
        self::assertFalse(ConsumptionTaxCategoryCode::UntaxedPurchase->isTaxable());
    }
}
