<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax\Service;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * 簡易課税 calculator.
 *
 * Net tax payable = output tax − (output tax × みなし仕入率)
 *                 = output tax × (1 − みなし仕入率 / 100)
 *
 * The 事業区分 is carried on the {@see ConsumptionTaxPeriod}. Purchase
 * transactions are ignored for payable calculation but are still summed
 * into the settlement breakdown for display.
 */
final class SimplifiedConsumptionTaxCalculator implements ConsumptionTaxCalculatorInterface
{
    public function calculate(ConsumptionTaxPeriod $period, array $transactions): ConsumptionTaxSettlement
    {
        if ($period->calculationMethod !== ConsumptionTaxCalculationMethod::Simplified) {
            throw ValidationException::withErrors([
                'calculationMethod' => ['period calculationMethod must be simplified.'],
            ]);
        }
        $businessCategory = $period->simplifiedBusinessCategory;
        if ($businessCategory === null) {
            throw ValidationException::withErrors([
                'simplifiedBusinessCategory' => ['simplifiedBusinessCategory is required for simplified method.'],
            ]);
        }

        $taxableSales    = '0.0000';
        $nonTaxableSales = '0.0000';
        $exemptSales     = '0.0000';
        $untaxedSales    = '0.0000';
        $outputTaxTotal  = '0.0000';

        /** @var array<string, string> $salesByRate */
        $salesByRate = [];
        /** @var array<string, string> $outputTaxByRate */
        $outputTaxByRate = [];
        /** @var array<string, string> $purchasesByRate */
        $purchasesByRate = [];

        foreach ($transactions as $t) {
            if (!$period->contains($t->bookedOn)) {
                continue;
            }
            $rateCode = PrincipleConsumptionTaxCalculator::rateCodeOf($t->ratePercent, $t->isReduced);
            if ($t->categoryCode->isSales()) {
                match ($t->categoryCode) {
                    ConsumptionTaxCategoryCode::TaxableSales => (function () use (
                        &$taxableSales, &$outputTaxTotal, &$salesByRate, &$outputTaxByRate, $t, $rateCode
                    ): void {
                        $taxableSales = Decimal::add($taxableSales, $t->amountExcludingTax);
                        $outputTaxTotal = Decimal::add($outputTaxTotal, $t->taxAmount);
                        $salesByRate[$rateCode] = Decimal::add($salesByRate[$rateCode] ?? '0.0000', $t->amountExcludingTax);
                        $outputTaxByRate[$rateCode] = Decimal::add($outputTaxByRate[$rateCode] ?? '0.0000', $t->taxAmount);
                    })(),
                    ConsumptionTaxCategoryCode::NonTaxableSales => $nonTaxableSales = Decimal::add($nonTaxableSales, $t->amountExcludingTax),
                    ConsumptionTaxCategoryCode::ExemptSales     => $exemptSales     = Decimal::add($exemptSales,     $t->amountExcludingTax),
                    ConsumptionTaxCategoryCode::UntaxedSales    => $untaxedSales    = Decimal::add($untaxedSales,    $t->amountExcludingTax),
                    default                                      => null,
                };
                continue;
            }
            if ($t->categoryCode->isDeductible()) {
                $purchasesByRate[$rateCode] = Decimal::add($purchasesByRate[$rateCode] ?? '0.0000', $t->amountExcludingTax);
            }
        }

        $ratio = $businessCategory->deemedPurchaseRatio();
        // deemed input tax = output tax × ratio / 100
        $deemedInput = self::multiplyPercent($outputTaxTotal, $ratio);
        $netPayable = Decimal::add($outputTaxTotal, '-' . ltrim(Decimal::normalize($deemedInput), '-'));

        $totalSales = Decimal::add(Decimal::add($taxableSales, $nonTaxableSales), $exemptSales);
        $taxableSalesRatio = self::ratio(Decimal::add($taxableSales, $exemptSales), $totalSales);

        return new ConsumptionTaxSettlement(
            period: $period,
            method: ConsumptionTaxCalculationMethod::Simplified,
            taxableSales: Decimal::normalize($taxableSales),
            nonTaxableSales: Decimal::normalize($nonTaxableSales),
            exemptSales: Decimal::normalize($exemptSales),
            untaxedSales: Decimal::normalize($untaxedSales),
            totalSales: Decimal::normalize($totalSales),
            taxableSalesRatio: $taxableSalesRatio,
            outputTax: Decimal::normalize($outputTaxTotal),
            deductibleInputTax: Decimal::normalize($deemedInput),
            adjustmentForNonRegistered: '0.0000',
            netTaxPayable: Decimal::normalize($netPayable),
            salesByRate: self::normalizeMap($salesByRate),
            outputTaxByRate: self::normalizeMap($outputTaxByRate),
            purchasesByRate: self::normalizeMap($purchasesByRate),
            inputTaxByRate: [],
        );
    }

    private static function multiplyPercent(string $amount, string $percent): string
    {
        if (function_exists('bcmul')) {
            return bcdiv(bcmul($amount, $percent, 8), '100', 4);
        }
        $v = ((float) $amount) * ((float) $percent) / 100.0;
        return number_format($v, 4, '.', '');
    }

    private static function ratio(string $num, string $den): string
    {
        if (Decimal::compare($den, '0.0000') === 0) {
            return '0.0000';
        }
        if (function_exists('bcdiv')) {
            return bcdiv($num, $den, 4);
        }
        $v = ((float) $num) / ((float) $den);
        return number_format($v, 4, '.', '');
    }

    /**
     * @param array<string, string> $map
     * @return array<string, string>
     */
    private static function normalizeMap(array $map): array
    {
        $out = [];
        foreach ($map as $k => $v) {
            $out[$k] = Decimal::normalize($v);
        }
        return $out;
    }
}
