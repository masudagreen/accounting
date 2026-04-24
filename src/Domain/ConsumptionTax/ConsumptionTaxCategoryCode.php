<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

/**
 * Canonical consumption-tax category codes.
 *
 * Mirrors the seed rows in `consumption_tax_categories`. Anything not
 * listed here is rejected at the domain boundary so calculators don't
 * have to branch on string typos.
 */
enum ConsumptionTaxCategoryCode: string
{
    case TaxableSales = 'taxable_sales';
    case NonTaxableSales = 'non_taxable_sales';
    case ExemptSales = 'exempt_sales';
    case UntaxedSales = 'untaxed_sales';
    case TaxablePurchase = 'taxable_purchase';
    case TaxablePurchaseNonRegistered = 'taxable_purchase_non_registered';
    case NonTaxablePurchase = 'non_taxable_purchase';
    case ExemptPurchase = 'exempt_purchase';
    case UntaxedPurchase = 'untaxed_purchase';

    public function side(): string
    {
        return match ($this) {
            self::TaxableSales,
            self::NonTaxableSales,
            self::ExemptSales,
            self::UntaxedSales => 'sales',
            default => 'purchase',
        };
    }

    public function isSales(): bool
    {
        return $this->side() === 'sales';
    }

    public function isPurchase(): bool
    {
        return $this->side() === 'purchase';
    }

    /**
     * Whether this category contributes to deductible input tax under
     * principle-method calculations.
     */
    public function isDeductible(): bool
    {
        return match ($this) {
            self::TaxablePurchase,
            self::TaxablePurchaseNonRegistered => true,
            default => false,
        };
    }

    public function isTaxable(): bool
    {
        return match ($this) {
            self::TaxableSales,
            self::TaxablePurchase,
            self::TaxablePurchaseNonRegistered => true,
            default => false,
        };
    }

    /**
     * Sales categories that count toward the 課税売上割合 numerator
     * (課税売上 + 免税売上).
     */
    public function isInTaxableSalesNumerator(): bool
    {
        return match ($this) {
            self::TaxableSales,
            self::ExemptSales => true,
            default => false,
        };
    }

    /**
     * Sales categories that count toward the 課税売上割合 denominator
     * (課税売上 + 非課税売上 + 免税売上, excluding 不課税).
     */
    public function isInTotalSalesDenominator(): bool
    {
        return match ($this) {
            self::TaxableSales,
            self::NonTaxableSales,
            self::ExemptSales => true,
            default => false,
        };
    }
}
