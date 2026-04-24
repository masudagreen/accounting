<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Immutable DTO representing one row in `fs_section_definitions`.
 *
 * `parentCode` is `null` for top-level sections (e.g. "資産の部" or the
 * standalone "売上高" line at the top of the PL).
 *
 * `formula` is a simple `+code` / `-code` list used by the
 * {@see \Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder}
 * when a section is a subtotal whose value is computed from siblings rather
 * than from child mappings (e.g. "売上総利益" = +operating_revenue - cost_of_sales).
 */
final readonly class FsSectionDefinition
{
    public function __construct(
        public FsKind $kind,
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
     * Examples:
     *   "+operating_revenue-cost_of_sales"
     *     → [[+1, 'operating_revenue'], [-1, 'cost_of_sales']]
     *
     * Returns an empty list if no formula is set.
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
