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
 * 2割特例 calculator.
 *
 * For formerly-免税 businesses that newly registered for the invoice
 * regime, net tax payable = output tax × 20%.
 *
 * This option is available for taxable periods that ended on or before
 * 2026-09-30; we don't enforce the sunset date at calculator level —
 * the application layer is responsible for gating the choice.
 */
final class TwoPercentConsumptionTaxCalculator implements ConsumptionTaxCalculatorInterface
{
    private const DEEMED_RATIO = '80';

    public function calculate(ConsumptionTaxPeriod $period, array $transactions): ConsumptionTaxSettlement
    {
        if ($period->calculationMethod !== ConsumptionTaxCalculationMethod::TwoPercent) {
            throw ValidationException::withErrors([
                'calculationMethod' => ['period calculationMethod must be two_percent.'],
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

        foreach ($transactions as $t) {
            if (!$period->contains($t->bookedOn)) {
                continue;
            }
            if (!$t->categoryCode->isSales()) {
                continue;
            }
            $rateCode = PrincipleConsumptionTaxCalculator::rateCodeOf($t->ratePercent, $t->isReduced);
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
        }

        $deemedInput = self::multiplyPercent($outputTaxTotal, self::DEEMED_RATIO);
        $netPayable = Decimal::add($outputTaxTotal, '-' . ltrim(Decimal::normalize($deemedInput), '-'));

        $totalSales = Decimal::add(Decimal::add($taxableSales, $nonTaxableSales), $exemptSales);
        $taxableSalesRatio = self::ratio(Decimal::add($taxableSales, $exemptSales), $totalSales);

        return new ConsumptionTaxSettlement(
            period: $period,
            method: ConsumptionTaxCalculationMethod::TwoPercent,
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
            purchasesByRate: [],
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
