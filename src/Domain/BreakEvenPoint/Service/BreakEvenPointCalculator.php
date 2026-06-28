<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint\Service;

use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Support\Decimal\Decimal;

/**
 * Core Cost-Volume-Profit engine. Stateless — everything flows through
 * {@see self::calculate()} so the service is trivially testable.
 *
 * Definitions (all scale-4):
 *   sales                   = Σ|balance| for rows with `account_category = revenue`
 *   variableCosts           = Σ amount × variable_ratio     (Variable / SemiVariable)
 *   fixedCosts              = Σ amount + amount × (1 - variable_ratio) for fixed / semi-variable
 *   contributionMargin      = sales - variableCosts
 *   contributionMarginRate  = contributionMargin / sales              (0 when sales == 0)
 *   bepSales                = fixedCosts / contributionMarginRate     (0 when CM rate == 0)
 *   bepRatio                = bepSales / sales                        (0 when sales == 0)
 *   safetyMarginRatio       = (sales - bepSales) / sales              (0 when sales == 0)
 *   operatingProfit         = contributionMargin - fixedCosts
 *
 * Expense-side row balances are taken as absolute values so the legacy
 * PL presentation ("費用 is positive") keeps working. Revenue rows use
 * |balance| for the same reason.
 */
final class BreakEvenPointCalculator
{
    public const RATIO_SCALE = 4;

    /**
     * @param list<AccountTitleCvpClassification> $classifications
     */
    public function calculate(
        string $entityId,
        string $fiscalTermId,
        \DateTimeImmutable $fromDate,
        \DateTimeImmutable $toDate,
        string $currencyCode,
        TrialBalance $trialBalance,
        array $classifications,
        \DateTimeImmutable $generatedAt,
    ): BreakEvenPointAnalysis {
        /** @var array<string, AccountTitleCvpClassification> $byId */
        $byId = [];
        foreach ($classifications as $c) {
            $byId[$c->accountTitleId] = $c;
        }

        $sales = '0.0000';
        $variable = '0.0000';
        $fixed = '0.0000';
        $salesBreakdown = [];
        $variableBreakdown = [];
        $fixedBreakdown = [];

        foreach ($trialBalance->rows as $row) {
            $abs = self::abs($row->balance);
            if ($row->accountCategory === 'revenue') {
                $sales = Decimal::add($sales, $abs);
                $salesBreakdown[] = [
                    'accountTitleId' => $row->accountTitleId,
                    'accountTitleCode' => $row->accountTitleCode,
                    'accountTitleName' => $row->accountTitleName,
                    'amount' => Decimal::normalize($abs),
                ];
                continue;
            }
            $cls = $byId[$row->accountTitleId] ?? null;
            if ($cls === null) {
                // Unclassified expense → conservative default: treat as fixed.
                if (self::isExpense($row)) {
                    $fixed = Decimal::add($fixed, $abs);
                    $fixedBreakdown[] = self::asBreakdownRow($row, CvpCostType::Fixed, $abs);
                }
                continue;
            }
            if (!self::isExpense($row)) {
                continue;
            }
            $varPart = self::multiply($abs, $cls->variableRatio);
            $fixedPart = self::subtract($abs, $varPart);
            if (Decimal::compare($varPart, '0.0000') > 0) {
                $variable = Decimal::add($variable, $varPart);
                $variableBreakdown[] = self::asBreakdownRow($row, $cls->costType, $varPart);
            }
            if (Decimal::compare($fixedPart, '0.0000') > 0) {
                $fixed = Decimal::add($fixed, $fixedPart);
                $fixedBreakdown[] = self::asBreakdownRow($row, $cls->costType, $fixedPart);
            }
        }

        $sales = Decimal::normalize($sales);
        $variable = Decimal::normalize($variable);
        $fixed = Decimal::normalize($fixed);

        $contributionMargin = self::subtract($sales, $variable);
        $operatingProfit = self::subtract($contributionMargin, $fixed);

        // Rates (%) are rounded half-up at scale 4 so 0.98785714… reports
        // as 0.9879 — that's what operators expect when reading "安全率".
        // Cash amounts (bepSales) stay truncated at scale 4 to match the
        // 円単位切り捨て convention shared with the trial-balance side.
        $cmRate = self::divideRatio($contributionMargin, $sales);
        $bepSales = self::divideAmount($fixed, $cmRate);
        $bepRatio = self::divideRatio($bepSales, $sales);
        $safety = Decimal::compare($sales, '0.0000') === 0
            ? '0.0000'
            : self::divideRatio(self::subtract($sales, $bepSales), $sales);

        return new BreakEvenPointAnalysis(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $fromDate,
            toDate: $toDate,
            currencyCode: $currencyCode,
            sales: $sales,
            variableCosts: $variable,
            fixedCosts: $fixed,
            contributionMargin: Decimal::normalize($contributionMargin),
            contributionMarginRate: Decimal::normalize($cmRate),
            bepSales: Decimal::normalize($bepSales),
            bepRatio: Decimal::normalize($bepRatio),
            safetyMarginRatio: Decimal::normalize($safety),
            operatingProfit: Decimal::normalize($operatingProfit),
            salesBreakdown: $salesBreakdown,
            variableBreakdown: $variableBreakdown,
            fixedBreakdown: $fixedBreakdown,
            generatedAt: $generatedAt,
        );
    }

    private static function isExpense(TrialBalanceRow $row): bool
    {
        // The canonical account_titles.category vocabulary is the 5-way
        // (asset/liability/equity/revenue/expense). The legacy PL/FS port
        // sometimes surfaces narrower synonyms (`cost_of_sales`,
        // `selling_admin`, `non_operating_expense`, `extraordinary_loss`)
        // when rows come from the FS mapping layer — accept both.
        return match ($row->accountCategory) {
            'expense', 'cost_of_sales', 'selling_admin', 'non_operating_expense', 'extraordinary_loss' => true,
            default => false,
        };
    }

    /**
     * @return array{accountTitleId:string, accountTitleCode:string, accountTitleName:string, costType:string, amount:string}
     */
    private static function asBreakdownRow(TrialBalanceRow $row, CvpCostType $type, string $amount): array
    {
        return [
            'accountTitleId' => $row->accountTitleId,
            'accountTitleCode' => $row->accountTitleCode,
            'accountTitleName' => $row->accountTitleName,
            'costType' => $type->value,
            'amount' => Decimal::normalize($amount),
        ];
    }

    private static function abs(string $v): string
    {
        $normalized = Decimal::normalize($v);

        return str_starts_with($normalized, '-') ? substr($normalized, 1) : $normalized;
    }

    private static function subtract(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @psalm-suppress ArgumentTypeCoercion BEP calculator only feeds Decimal-normalised numeric strings */
            return bcsub($a, $b, Decimal::SCALE);
        }
        $negB = Decimal::compare($b, '0.0000') === 0
            ? '0.0000'
            : (str_starts_with($b, '-') ? substr($b, 1) : '-'.$b);

        return Decimal::add($a, $negB);
    }

    private static function multiply(string $a, string $b): string
    {
        if (function_exists('bcmul')) {
            /** @psalm-suppress ArgumentTypeCoercion BEP calculator only feeds Decimal-normalised numeric strings */
            return bcmul($a, $b, Decimal::SCALE);
        }
        $fa = (float) $a;
        $fb = (float) $b;
        $product = $fa * $fb;

        return number_format($product, Decimal::SCALE, '.', '');
    }

    /**
     * Amount-shaped division (円単位を 4 桁まで保持して切り捨て). Mirrors
     * the historical {@see divideOrZero} behaviour — bcdiv at scale 4 is
     * pure truncation, matching how the trial-balance side stores values.
     */
    private static function divideAmount(string $a, string $b): string
    {
        if (Decimal::compare($b, '0.0000') === 0) {
            return '0.0000';
        }
        if (function_exists('bcdiv')) {
            /** @psalm-suppress ArgumentTypeCoercion BEP calculator only feeds Decimal-normalised numeric strings */
            return bcdiv($a, $b, Decimal::SCALE);
        }
        $fa = (float) $a;
        $fb = (float) $b;
        if ($fb === 0.0) {
            return '0.0000';
        }

        return number_format($fa / $fb, Decimal::SCALE, '.', '');
    }

    /**
     * Ratio-shaped division: 4 桁 half-up rounding. Used for 寄与率 / BEP
     * 比率 / 安全率 where operators read the value as a percentage and
     * expect "0.98785714…" → 0.9879, not the bcdiv-trunc 0.9878.
     */
    private static function divideRatio(string $a, string $b): string
    {
        if (Decimal::compare($b, '0.0000') === 0) {
            return '0.0000';
        }
        if (function_exists('bcdiv')) {
            /** @psalm-suppress ArgumentTypeCoercion BEP calculator only feeds Decimal-normalised numeric strings */
            $raw = bcdiv($a, $b, Decimal::SCALE + 1);

            return self::roundHalfUp($raw, Decimal::SCALE);
        }
        $fa = (float) $a;
        $fb = (float) $b;
        if ($fb === 0.0) {
            return '0.0000';
        }

        return number_format(round($fa / $fb, Decimal::SCALE), Decimal::SCALE, '.', '');
    }

    /**
     * Round a decimal string to the given scale, half-up. Implemented
     * digit-by-digit so we never round-trip through float and lose
     * precision on long fractions (the very thing bcdiv was solving).
     */
    private static function roundHalfUp(string $value, int $scale): string
    {
        $negative = $value !== '' && $value[0] === '-';
        if ($negative) {
            $value = substr($value, 1);
        }
        [$int, $frac] = array_pad(explode('.', $value, 2), 2, '');
        $frac = str_pad($frac, $scale + 1, '0');
        $kept = substr($frac, 0, $scale);
        $rounder = (int) $frac[$scale];
        if ($rounder >= 5) {
            $digits = str_split($kept);
            $carry = 1;
            for ($i = count($digits) - 1; $i >= 0 && $carry; --$i) {
                $d = (int) $digits[$i] + $carry;
                if ($d >= 10) {
                    $digits[$i] = '0';
                } else {
                    $digits[$i] = (string) $d;
                    $carry = 0;
                }
            }
            $kept = implode('', $digits);
            if ($carry === 1) {
                $intDigits = str_split($int === '' ? '0' : $int);
                for ($i = count($intDigits) - 1; $i >= 0 && $carry; --$i) {
                    $d = (int) $intDigits[$i] + $carry;
                    if ($d >= 10) {
                        $intDigits[$i] = '0';
                    } else {
                        $intDigits[$i] = (string) $d;
                        $carry = 0;
                    }
                }
                if ($carry === 1) {
                    array_unshift($intDigits, '1');
                }
                $int = implode('', $intDigits);
            }
        }
        $out = ($int === '' ? '0' : $int).'.'.$kept;

        return $negative && Decimal::compare($out, '0.'.str_repeat('0', $scale)) !== 0
            ? '-'.$out
            : $out;
    }
}
