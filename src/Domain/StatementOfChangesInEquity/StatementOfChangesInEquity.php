<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

use DateTimeImmutable;
use Rucaro\Support\Decimal\Decimal;

/**
 * Read-model aggregate for the 株主資本等変動計算書 (Statement of
 * Changes in Equity, "SS").
 *
 * One instance spans the fiscal-term window (`fromDate`〜`toDate`),
 * carrying a column per {@see SsSectionCode} plus a synthetic
 * "合計" (total) pseudo-column surfaced via {@see self::totals()}.
 *
 * Built once by {@see Service\StatementOfChangesInEquityBuilder} and
 * handed unchanged to the serializer / PDF renderer.
 */
final readonly class StatementOfChangesInEquity
{
    /**
     * @param list<SsSection> $sections
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public string $currencyCode,
        public array $sections,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    /**
     * Opening / changes / ending totals across every column (the 合計
     * column of the legacy layout).
     *
     * @return array{opening: string, totalChange: string, ending: string}
     */
    public function totals(): array
    {
        $opening = '0.0000';
        $ending  = '0.0000';
        $delta   = '0.0000';
        foreach ($this->sections as $section) {
            $opening = Decimal::add($opening, $section->openingBalance);
            $ending  = Decimal::add($ending, $section->endingBalance);
            $delta   = Decimal::add($delta, $section->totalChange());
        }
        return [
            'opening'     => Decimal::normalize($opening),
            'totalChange' => Decimal::normalize($delta),
            'ending'      => Decimal::normalize($ending),
        ];
    }

    /**
     * Look up a column by section code. Returns `null` when absent so
     * templates can skip columns without crashing.
     */
    public function sectionByCode(SsSectionCode $code): ?SsSection
    {
        foreach ($this->sections as $section) {
            if ($section->sectionCode === $code) {
                return $section;
            }
        }
        return null;
    }

    /**
     * Union of every {@see SsChangeType} actually used across any
     * section — determines the row set the table renderer emits.
     *
     * @return list<SsChangeType>
     */
    public function changeTypesInUse(): array
    {
        $seen = [];
        foreach ($this->sections as $section) {
            foreach ($section->changes as $change) {
                $seen[$change->changeType->value] = $change->changeType;
            }
        }
        return array_values($seen);
    }
}
