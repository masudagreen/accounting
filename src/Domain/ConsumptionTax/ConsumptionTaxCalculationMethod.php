<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

/**
 * Calculation method chosen for a single {@see ConsumptionTaxPeriod}.
 *
 *   - Principle  : 原則課税 (output tax − input tax)
 *   - Simplified : 簡易課税 (output tax × deemed-purchase-ratio by business category)
 *   - TwoPercent : 2 割特例 (output tax × 20%; インボイス登録した免税事業者向け救済)
 */
enum ConsumptionTaxCalculationMethod: string
{
    case Principle = 'principle';
    case Simplified = 'simplified';
    case TwoPercent = 'two_percent';

    public function label(): string
    {
        return match ($this) {
            self::Principle  => '原則課税',
            self::Simplified => '簡易課税',
            self::TwoPercent => '2割特例',
        };
    }
}
