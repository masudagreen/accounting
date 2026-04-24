<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

use Rucaro\Support\Decimal\Decimal;

/**
 * Single variation row inside a {@see SsSection} (one column's worth
 * of motion: 配当 −12,000,000, 当期純利益 +45,000,000, etc.).
 *
 * `source` distinguishes rows the {@see Service\StatementOfChangesInEquityBuilder}
 * auto-detected from posted journals (`journal_auto`) from those the
 * reviewer explicitly typed into {@see SsManualAdjustment} rows
 * (`manual`). The UI surfaces this so operators can see at a glance
 * whether a figure is recomputable or requires a maintenance touch.
 */
final readonly class SsChange
{
    public const SOURCE_JOURNAL_AUTO = 'journal_auto';
    public const SOURCE_MANUAL       = 'manual';

    public function __construct(
        public SsChangeType $changeType,
        public string $label,
        public string $amount,
        public string $source,
    ) {
    }

    /**
     * Normalised copy with `$amount` rounded to scale-4. Factory
     * helper used by the builder so freshly-constructed rows never
     * leak floats or inconsistent trailing zeros into callers.
     */
    public static function of(
        SsChangeType $type,
        string $label,
        string $amount,
        string $source = self::SOURCE_MANUAL,
    ): self {
        return new self(
            changeType: $type,
            label: $label === '' ? $type->label() : $label,
            amount: Decimal::normalize($amount),
            source: $source === self::SOURCE_JOURNAL_AUTO ? self::SOURCE_JOURNAL_AUTO : self::SOURCE_MANUAL,
        );
    }
}
