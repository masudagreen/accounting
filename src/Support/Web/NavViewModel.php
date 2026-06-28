<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

/**
 * Layout payload for the persistent top navigation bar (entity / fiscal-term
 * selectors). Controllers used to build this ad-hoc with mismatched key
 * names; the nav template now expects a single canonical shape.
 *
 * Shape returned by {@see NavViewModel::buildPayload()}:
 *
 *     [
 *         'entities'                   => list<['id', 'name']>,
 *         'nav_fiscal_terms'           => list<['id', 'fiscalPeriod', 'startDate', 'endDate', 'entityId']>,
 *         'nav_fiscal_terms_by_entity' => array<entity_id, list<term shape>>,
 *         'selected_entity_id'         => ?string,
 *         'selected_fiscal_term_id'    => ?string,
 *     ]
 *
 * The class is intentionally small and stateless so each controller can
 * `array_merge($payload, $nav->buildPayload(...))` without dragging in
 * additional dependencies. The actual entity / term lookups live in the
 * caller — controllers that already query them (every Journal / Report
 * controller does) can pass the data straight in.
 */
final class NavViewModel
{
    /**
     * @param list<array{id: string, name: string}> $entities
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $fiscalTerms
     *
     * @return array{
     *     entities: list<array{id: string, name: string}>,
     *     nav_fiscal_terms: list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}>,
     *     selected_entity_id: string,
     *     selected_fiscal_term_id: string,
     * }
     */
    public static function buildPayload(
        array $entities,
        array $fiscalTerms,
        ?string $selectedEntityId,
        ?string $selectedFiscalTermId,
    ): array {
        return [
            'entities' => $entities,
            'nav_fiscal_terms' => $fiscalTerms,
            'selected_entity_id' => $selectedEntityId ?? '',
            'selected_fiscal_term_id' => $selectedFiscalTermId ?? '',
        ];
    }

    /**
     * Format a single fiscal-term option label for the nav-bar dropdown.
     * Centralised so the navbar template, the form template, and unit tests
     * all agree on the canonical "第 N 期 (YYYY/MM/DD 〜 YYYY/MM/DD)" shape.
     *
     * @param array{fiscalPeriod: int, startDate: string, endDate: string} $term
     */
    public static function fiscalTermOptionLabel(array $term): string
    {
        $start = self::formatDateForLabel($term['startDate']);
        $end = self::formatDateForLabel($term['endDate']);

        return '第 '.$term['fiscalPeriod'].' 期 ('.$start.' 〜 '.$end.')';
    }

    private static function formatDateForLabel(string $iso): string
    {
        if ($iso === '' || strlen($iso) < 10) {
            return $iso;
        }

        // ISO `YYYY-MM-DD` → `YYYY/MM/DD` for the label, leaving the raw
        // value in the option's `value` attribute untouched.
        return substr($iso, 0, 4).'/'.substr($iso, 5, 2).'/'.substr($iso, 8, 2);
    }
}
