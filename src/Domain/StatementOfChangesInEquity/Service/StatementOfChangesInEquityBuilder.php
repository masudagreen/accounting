<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity\Service;

use DateTimeImmutable;
use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSection;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;
use Rucaro\Support\Decimal\Decimal;

/**
 * Pure domain service that assembles a {@see StatementOfChangesInEquity}
 * from three inputs:
 *
 *  1. a map of opening balances keyed by {@see SsSectionCode};
 *  2. a list of {@see SsManualAdjustment} rows for the same window;
 *  3. an optional net-income amount (sourced upstream from the PL
 *     builder) which is folded into the RetainedEarnings column as
 *     a `journal_auto` {@see SsChange}.
 *
 * Consciously minimal: Wave 6-H-2 does not attempt to rebuild the
 * legacy "journal scan" auto-detection that `FinancialStatementSS.php`
 * ran. That legacy path relied on `_checkUseLog` scanning a JGAAP
 * log table which no longer exists in the new schema. When finer-
 * grained auto detection ships, it will plug in via additional
 * `SsChange` rows with `source = journal_auto` rather than by
 * widening this interface.
 *
 * Pure: no DB / clock / I-O. Testable in isolation.
 */
final class StatementOfChangesInEquityBuilder
{
    /**
     * @param array<string, string>      $openingBalances section_code => decimal
     * @param list<SsManualAdjustment>   $adjustments
     */
    public function build(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate,
        string $currencyCode,
        array $openingBalances,
        array $adjustments,
        ?string $netIncome,
        DateTimeImmutable $generatedAt,
    ): StatementOfChangesInEquity {
        $adjustmentsBySection = self::groupAdjustments($adjustments);

        $sections = [];
        foreach (SsSectionCode::ordered() as $code) {
            $opening = self::safeOpening($openingBalances, $code);

            /** @var list<SsChange> $changes */
            $changes = [];
            foreach ($adjustmentsBySection[$code->value] ?? [] as $adj) {
                $changes[] = $adj->toSsChange();
            }

            // Fold net income into RetainedEarnings as a journal_auto row.
            if ($code === SsSectionCode::RetainedEarnings
                && $netIncome !== null
                && Decimal::compare($netIncome, '0.0000') !== 0
            ) {
                $changes[] = SsChange::of(
                    SsChangeType::NetIncome,
                    SsChangeType::NetIncome->label(),
                    $netIncome,
                    SsChange::SOURCE_JOURNAL_AUTO,
                );
            }

            $sections[] = SsSection::fromChanges($code, $opening, $changes);
        }

        return new StatementOfChangesInEquity(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $fromDate,
            toDate: $toDate,
            currencyCode: $currencyCode,
            sections: $sections,
            generatedAt: $generatedAt,
        );
    }

    /**
     * @param list<SsManualAdjustment> $adjustments
     * @return array<string, list<SsManualAdjustment>>
     */
    private static function groupAdjustments(array $adjustments): array
    {
        // Stable sort by (sortOrder ASC, id ASC) before grouping so the
        // template output stays deterministic across reloads.
        usort($adjustments, static function (SsManualAdjustment $a, SsManualAdjustment $b): int {
            if ($a->sortOrder !== $b->sortOrder) {
                return $a->sortOrder <=> $b->sortOrder;
            }
            return $a->id <=> $b->id;
        });

        $out = [];
        foreach ($adjustments as $adj) {
            $out[$adj->sectionCode->value][] = $adj;
        }
        return $out;
    }

    /**
     * @param array<string, string> $openingBalances
     */
    private static function safeOpening(array $openingBalances, SsSectionCode $code): string
    {
        $raw = $openingBalances[$code->value] ?? '0.0000';
        if ($raw === '' || !is_numeric($raw)) {
            return '0.0000';
        }
        return Decimal::normalize($raw);
    }
}
