<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement;

use Rucaro\Support\Decimal\Decimal;

/**
 * Logical grouping of {@see FinancialStatementLine}s within a statement
 * (e.g. "流動資産" on the BS, "売上高" on the PL, "営業CF" on the CS).
 *
 * Maintains `code` so renderers and tests can look up a specific section
 * without relying on Japanese labels. The subtotal is recomputed from the
 * lines so Section remains a value object — callers cannot hand in a stale
 * subtotal independent of the line set.
 */
final readonly class Section
{
    public const CODE_ASSETS         = 'assets';
    public const CODE_LIABILITIES    = 'liabilities';
    public const CODE_EQUITY         = 'equity';
    public const CODE_REVENUE        = 'revenue';
    public const CODE_EXPENSES       = 'expenses';
    public const CODE_OPERATING_CF   = 'operating';
    public const CODE_INVESTING_CF   = 'investing';
    public const CODE_FINANCING_CF   = 'financing';

    /**
     * @param list<FinancialStatementLine> $lines
     */
    public function __construct(
        public string $code,
        public string $label,
        public array $lines,
        public string $subtotal,
        public ?string $parentCode = null,
        public int $sortOrder = 0,
        public bool $isSubtotal = false,
        public bool $isTotal = false,
    ) {
    }

    /**
     * Build a Section whose subtotal is the scale-4 sum of every non-subtotal
     * line's amount. Callers that want a custom subtotal (e.g. equity with
     * current-period net income folded in) should construct directly.
     *
     * @param list<FinancialStatementLine> $lines
     */
    public static function fromLines(string $code, string $label, array $lines): self
    {
        $sum = '0.0000';
        foreach ($lines as $line) {
            if ($line->isSubtotal) {
                continue;
            }
            $sum = Decimal::add($sum, $line->amount);
        }
        return new self(
            code: $code,
            label: $label,
            lines: $lines,
            subtotal: Decimal::normalize($sum),
        );
    }

    /**
     * Returns a new Section with an overridden subtotal (lines unchanged).
     */
    public function withSubtotal(string $subtotal): self
    {
        return new self(
            code: $this->code,
            label: $this->label,
            lines: $this->lines,
            subtotal: Decimal::normalize($subtotal),
            parentCode: $this->parentCode,
            sortOrder: $this->sortOrder,
            isSubtotal: $this->isSubtotal,
            isTotal: $this->isTotal,
        );
    }

    /**
     * Returns a new Section with an extra line appended and its subtotal
     * recomputed from scratch (hierarchy metadata is preserved).
     */
    public function withAppendedLine(FinancialStatementLine $line): self
    {
        $lines = $this->lines;
        $lines[] = $line;
        $rebuilt = self::fromLines($this->code, $this->label, $lines);
        return new self(
            code: $rebuilt->code,
            label: $rebuilt->label,
            lines: $rebuilt->lines,
            subtotal: $rebuilt->subtotal,
            parentCode: $this->parentCode,
            sortOrder: $this->sortOrder,
            isSubtotal: $this->isSubtotal,
            isTotal: $this->isTotal,
        );
    }
}
