<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

/**
 * Immutable DTO representing one row in `fs_cs_section_definitions`.
 *
 * Mirrors {@see \Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition}
 * but specialised for the Cash Flow Statement. CS has a distinct hierarchy
 * (operating / investing / financing plus beginning/ending cash), so a
 * separate DTO keeps the BS/PL code path untouched.
 *
 * `parentCode` is `null` for top-level nodes (I. / II. / III., および期末残高).
 *
 * `formula` uses the same `+code` / `-code` grammar as the BS/PL builder,
 * for example:
 *   - `operating_cf_total` = `+operating_cf_subtotal+interest_received-interest_paid-tax_paid`
 *   - `net_change_in_cash` = `+operating_cf_total+investing_cf_total+financing_cf_total`
 *   - `ending_cash`        = `+net_change_in_cash+beginning_cash`
 */
final readonly class CsSectionDefinition
{
    public function __construct(
        public string $code,
        public ?string $parentCode,
        public string $label,
        public int $sortOrder,
        public bool $isSubtotal,
        public bool $isTotal,
        public ?string $formula,
    ) {
    }

    /**
     * Parse `formula` into an ordered list of [sign, code] tuples.
     *
     * Returns an empty list if no formula is set. Mirrors
     * {@see \Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition::parsedFormula()}
     * so the CS builder can reuse the same +/- grammar without a second parser.
     *
     * @return list<array{0: int, 1: string}>
     */
    public function parsedFormula(): array
    {
        if ($this->formula === null || $this->formula === '') {
            return [];
        }
        $out = [];
        $sign = 1;
        $buffer = '';
        $length = strlen($this->formula);
        for ($i = 0; $i < $length; $i++) {
            $ch = $this->formula[$i];
            if ($ch === '+' || $ch === '-') {
                if ($buffer !== '') {
                    $out[] = [$sign, $buffer];
                    $buffer = '';
                }
                $sign = $ch === '-' ? -1 : 1;
                continue;
            }
            $buffer .= $ch;
        }
        if ($buffer !== '') {
            $out[] = [$sign, $buffer];
        }
        return $out;
    }
}
