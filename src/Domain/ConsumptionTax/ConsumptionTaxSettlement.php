<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use Rucaro\Support\Decimal\Decimal;

/**
 * Immutable result aggregate produced by a calculator.
 *
 * All decimal fields are scale-4 strings (`Decimal::SCALE = 4`).
 * Rate-indexed breakdown maps use the rate code (e.g. `standard_10`,
 * `reduced_8`, `old_8`) as key.
 *
 * Legacy parallel: the old `jsonConsumptionTaxDetail` column in
 * `accountingFSValueJpn` carried roughly the same data but as a nested
 * JSON blob; we model it as a first-class aggregate here.
 */
final readonly class ConsumptionTaxSettlement
{
    /**
     * @param array<string, string> $salesByRate       rate_code => amount (excl. tax)
     * @param array<string, string> $outputTaxByRate   rate_code => tax amount
     * @param array<string, string> $purchasesByRate   rate_code => amount (excl. tax)
     * @param array<string, string> $inputTaxByRate    rate_code => tax amount
     */
    public function __construct(
        public ConsumptionTaxPeriod $period,
        public ConsumptionTaxCalculationMethod $method,
        public string $taxableSales,
        public string $nonTaxableSales,
        public string $exemptSales,
        public string $untaxedSales,
        public string $totalSales,
        public string $taxableSalesRatio,
        public string $outputTax,
        public string $deductibleInputTax,
        public string $adjustmentForNonRegistered,
        public string $netTaxPayable,
        public array $salesByRate,
        public array $outputTaxByRate,
        public array $purchasesByRate,
        public array $inputTaxByRate,
    ) {
    }

    /**
     * Split the total tax into the national (7.8% / 6.24%) vs local
     * (2.2% / 1.76%) components. This is the breakdown required on the
     * 消費税確定申告書.
     *
     * Standard 10%: national 7.8% + local 2.2%  (ratio 78/22)
     * Reduced 8%:   national 6.24% + local 1.76% (ratio 78/22)
     * Old 8%:       national 6.3% + local 1.7%   (ratio 63/17, ~78.75/21.25)
     *
     * We use the legacy 78/22 ratio for 10% and the pre-2019 17/63 ratio
     * for old_8, matching the National Tax Agency worksheet.
     *
     * @return array{national:string, local:string}
     */
    public function taxSplitNationalLocal(): array
    {
        $national = '0.0000';
        $local = '0.0000';
        foreach ($this->outputTaxByRate as $rateCode => $taxAmount) {
            $n = self::nationalPortion($rateCode, $taxAmount);
            $national = Decimal::add($national, $n);
            $local = Decimal::add($local, Decimal::normalize(self::subtract($taxAmount, $n)));
        }
        foreach ($this->inputTaxByRate as $rateCode => $taxAmount) {
            $n = self::nationalPortion($rateCode, $taxAmount);
            $national = Decimal::add($national, '-' . ltrim($n, '-'));
            $local = Decimal::add($local, '-' . ltrim(self::subtract($taxAmount, $n), '-'));
        }
        return [
            'national' => Decimal::normalize($national),
            'local'    => Decimal::normalize($local),
        ];
    }

    private static function nationalPortion(string $rateCode, string $taxAmount): string
    {
        // Use the percentages set by the 消費税法施行令.
        return match ($rateCode) {
            'standard_10' => self::multiplyRatio($taxAmount, '78', '100'),
            'reduced_8'   => self::multiplyRatio($taxAmount, '78', '100'),
            'old_8'       => self::multiplyRatio($taxAmount, '63', '80'),
            'old_5'       => self::multiplyRatio($taxAmount, '4',  '5'),
            'old_3'       => $taxAmount,
            default       => $taxAmount,
        };
    }

    private static function multiplyRatio(string $amount, string $num, string $den): string
    {
        if (function_exists('bcmul')) {
            return bcdiv(bcmul($amount, $num, 8), $den, 4);
        }
        $v = ((float) $amount) * ((float) $num) / ((float) $den);
        return number_format($v, 4, '.', '');
    }

    private static function subtract(string $a, string $b): string
    {
        return Decimal::add($a, '-' . ltrim(Decimal::normalize($b), '-'));
    }
}
