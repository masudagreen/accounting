<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax\Service;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * 原則課税 calculator.
 *
 * Net tax payable = output tax (on 課税売上)
 *                  − deductible input tax (on 課税仕入)
 *                  − transitional-measure adjustment (for 非登録事業者
 *                    からの仕入)
 *
 * Non-taxable / exempt / untaxed transactions contribute to the
 * {@see ConsumptionTaxSettlement} breakdown but carry zero tax.
 */
final class PrincipleConsumptionTaxCalculator implements ConsumptionTaxCalculatorInterface
{
    public function __construct(
        private readonly InvoiceDeductionCalculator $invoiceDeduction = new InvoiceDeductionCalculator(),
    ) {
    }

    public function calculate(ConsumptionTaxPeriod $period, array $transactions): ConsumptionTaxSettlement
    {
        if ($period->calculationMethod !== ConsumptionTaxCalculationMethod::Principle) {
            throw ValidationException::withErrors([
                'calculationMethod' => ['period calculationMethod must be principle.'],
            ]);
        }

        $taxableSales    = '0.0000';
        $nonTaxableSales = '0.0000';
        $exemptSales     = '0.0000';
        $untaxedSales    = '0.0000';
        $outputTaxTotal  = '0.0000';
        $inputTaxTotal   = '0.0000';
        $adjustment      = '0.0000';

        /** @var array<string, string> $salesByRate */
        $salesByRate = [];
        /** @var array<string, string> $outputTaxByRate */
        $outputTaxByRate = [];
        /** @var array<string, string> $purchasesByRate */
        $purchasesByRate = [];
        /** @var array<string, string> $inputTaxByRate */
        $inputTaxByRate = [];

        foreach ($transactions as $t) {
            if (!$period->contains($t->bookedOn)) {
                continue;
            }
            $rateCode = self::rateCodeOf($t->ratePercent, $t->isReduced);

            if ($t->categoryCode->isSales()) {
                match ($t->categoryCode) {
                    ConsumptionTaxCategoryCode::TaxableSales => (function () use (
                        &$taxableSales, &$outputTaxTotal, &$salesByRate, &$outputTaxByRate, $t, $rateCode
                    ): void {
                        $taxableSales = Decimal::add($taxableSales, $t->amountExcludingTax);
                        $outputTaxTotal = Decimal::add($outputTaxTotal, $t->taxAmount);
                        self::addTo($salesByRate, $rateCode, $t->amountExcludingTax);
                        self::addTo($outputTaxByRate, $rateCode, $t->taxAmount);
                    })(),
                    ConsumptionTaxCategoryCode::NonTaxableSales => $nonTaxableSales = Decimal::add($nonTaxableSales, $t->amountExcludingTax),
                    ConsumptionTaxCategoryCode::ExemptSales     => $exemptSales     = Decimal::add($exemptSales,     $t->amountExcludingTax),
                    ConsumptionTaxCategoryCode::UntaxedSales    => $untaxedSales    = Decimal::add($untaxedSales,    $t->amountExcludingTax),
                    default                                      => null,
                };
                continue;
            }

            if ($t->categoryCode === ConsumptionTaxCategoryCode::TaxablePurchase) {
                $inputTaxTotal = Decimal::add($inputTaxTotal, $t->taxAmount);
                self::addTo($purchasesByRate, $rateCode, $t->amountExcludingTax);
                self::addTo($inputTaxByRate,  $rateCode, $t->taxAmount);
                continue;
            }
            if ($t->categoryCode === ConsumptionTaxCategoryCode::TaxablePurchaseNonRegistered) {
                $disallowed = $this->invoiceDeduction->disallowedAmount($t->bookedOn, $t->taxAmount);
                $deductible = $this->invoiceDeduction->deductibleAmount($t->bookedOn, $t->taxAmount);
                $adjustment = Decimal::add($adjustment, $disallowed);
                $inputTaxTotal = Decimal::add($inputTaxTotal, $deductible);
                self::addTo($purchasesByRate, $rateCode, $t->amountExcludingTax);
                self::addTo($inputTaxByRate,  $rateCode, $deductible);
            }
        }

        $totalSales = Decimal::add(Decimal::add($taxableSales, $nonTaxableSales), $exemptSales);
        $taxableSalesRatio = self::ratio(Decimal::add($taxableSales, $exemptSales), $totalSales);

        // Net payable: output − deductible input. `adjustment` is already
        // folded into inputTaxTotal via the deductible portion; we surface
        // it in the settlement so the report can show the number.
        $netPayable = Decimal::add($outputTaxTotal, '-' . ltrim(Decimal::normalize($inputTaxTotal), '-'));

        return new ConsumptionTaxSettlement(
            period: $period,
            method: ConsumptionTaxCalculationMethod::Principle,
            taxableSales: Decimal::normalize($taxableSales),
            nonTaxableSales: Decimal::normalize($nonTaxableSales),
            exemptSales: Decimal::normalize($exemptSales),
            untaxedSales: Decimal::normalize($untaxedSales),
            totalSales: Decimal::normalize($totalSales),
            taxableSalesRatio: $taxableSalesRatio,
            outputTax: Decimal::normalize($outputTaxTotal),
            deductibleInputTax: Decimal::normalize($inputTaxTotal),
            adjustmentForNonRegistered: Decimal::normalize($adjustment),
            netTaxPayable: Decimal::normalize($netPayable),
            salesByRate: self::normalizeMap($salesByRate),
            outputTaxByRate: self::normalizeMap($outputTaxByRate),
            purchasesByRate: self::normalizeMap($purchasesByRate),
            inputTaxByRate: self::normalizeMap($inputTaxByRate),
        );
    }

    /**
     * @param array<string, string> $map
     */
    private static function addTo(array &$map, string $key, string $v): void
    {
        $cur = $map[$key] ?? '0.0000';
        $map[$key] = Decimal::add($cur, $v);
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

    public static function rateCodeOf(string $ratePercent, bool $isReduced): string
    {
        // Accept both "10" and "10.00" / "8.00" etc.
        $n = (float) $ratePercent;
        if ($isReduced && abs($n - 8.0) < 0.01) {
            return 'reduced_8';
        }
        if (abs($n - 10.0) < 0.01) {
            return 'standard_10';
        }
        if (abs($n - 8.0) < 0.01) {
            return 'old_8';
        }
        if (abs($n - 5.0) < 0.01) {
            return 'old_5';
        }
        if (abs($n - 3.0) < 0.01) {
            return 'old_3';
        }
        return 'untaxed';
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
}
