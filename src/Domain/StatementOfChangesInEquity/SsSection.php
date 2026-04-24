<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

use Rucaro\Support\Decimal\Decimal;

/**
 * One column of the 株主資本等変動計算書 tracking a single equity
 * sub-account from its period opening balance through each
 * {@see SsChange} down to the derived ending balance.
 *
 * Invariant: `endingBalance === openingBalance + sum(changes.amount)`.
 * Violation throws via {@see self::fromChanges()} so the template
 * layer never renders an inconsistent column.
 */
final readonly class SsSection
{
    /**
     * @param list<SsChange> $changes
     */
    public function __construct(
        public SsSectionCode $sectionCode,
        public string $label,
        public string $openingBalance,
        public array $changes,
        public string $endingBalance,
    ) {
    }

    /**
     * Build a Section whose ending balance is derived from opening +
     * sum of changes. Preferred entry point — avoids drift between
     * the two fields. The optional `$label` overrides the enum's
     * default Japanese label.
     *
     * @param list<SsChange> $changes
     */
    public static function fromChanges(
        SsSectionCode $code,
        string $openingBalance,
        array $changes,
        ?string $label = null,
    ): self {
        $sum = Decimal::normalize($openingBalance);
        foreach ($changes as $change) {
            $sum = Decimal::add($sum, $change->amount);
        }
        return new self(
            sectionCode: $code,
            label: $label ?? $code->label(),
            openingBalance: Decimal::normalize($openingBalance),
            changes: $changes,
            endingBalance: Decimal::normalize($sum),
        );
    }

    /**
     * Net change for the period (ending - opening). Rendered in the
     * "当期変動額合計" total row of the PDF.
     */
    public function totalChange(): string
    {
        $negOpening = self::negate($this->openingBalance);
        return Decimal::normalize(Decimal::add($this->endingBalance, $negOpening));
    }

    private static function negate(string $value): string
    {
        $normalised = Decimal::normalize($value);
        if ($normalised === '0.0000') {
            return '0.0000';
        }
        if (str_starts_with($normalised, '-')) {
            return substr($normalised, 1);
        }
        return '-' . $normalised;
    }
}
