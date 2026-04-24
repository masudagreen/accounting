<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement;

use DateTimeZone;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Port\FsSectionCode;
use Rucaro\Domain\FinancialStatement\Section;

/**
 * Pure-function serializer that turns a {@see FinancialStatement} aggregate
 * into the array shape defined under
 * `#/components/schemas/FinancialStatements` in `docs/api/openapi.yaml`.
 *
 * Kept separate from the HTTP controller so tests can assert on the shape
 * without booting FastRoute.
 *
 * Output shape (BS example):
 *   bs:
 *     sections:            # full J-GAAP hierarchy (sort_order ascending)
 *       - code, parentCode, label, sortOrder, isSubtotal, isTotal, subtotal, lines
 *     assets / liabilities / equity   # legacy flat keys kept for back-compat
 *     totals: { assets, liabilities, equity }
 *
 * The `sections` key lets API clients reconstruct the tree via `parentCode`;
 * the flat keys remain so simplified (non-port) statements keep their old
 * contract.
 */
final class JsonFinancialStatementSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(FinancialStatement $fs): array
    {
        return [
            'entityId'     => $fs->entityId,
            'fiscalTermId' => $fs->fiscalTermId,
            'kind'         => $fs->kind->value,
            'fromDate'     => $fs->fromDate->format('Y-m-d'),
            'asOf'         => $fs->toDate->format('Y-m-d'),
            'currencyCode' => $fs->currencyCode,
            'bs'           => $fs->bs === [] ? null : self::serializeBs($fs),
            'pl'           => $fs->pl === [] ? null : self::serializePl($fs),
            'cs'           => $fs->cs === [] ? null : self::serializeCs($fs),
            'totals'       => $fs->totals,
            'generatedAt'  => $fs->generatedAt
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeBs(FinancialStatement $fs): array
    {
        return [
            'sections'    => self::orderedSections($fs->bs),
            // Legacy flat keys: preserved so the simplified path keeps its
            // existing contract and API clients that only read the three
            // top-level rollups keep working unchanged.
            'assets'      => self::section(
                $fs->bs[FsSectionCode::BS_ASSET]
                    ?? $fs->bs[Section::CODE_ASSETS]
                    ?? null,
            ),
            'liabilities' => self::section(
                $fs->bs[FsSectionCode::BS_LIABILITY]
                    ?? $fs->bs[Section::CODE_LIABILITIES]
                    ?? null,
            ),
            'equity'      => self::section(
                $fs->bs[FsSectionCode::BS_EQUITY]
                    ?? $fs->bs[Section::CODE_EQUITY]
                    ?? null,
            ),
            'totals'      => self::bsTotals($fs),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializePl(FinancialStatement $fs): array
    {
        return [
            'sections'  => self::orderedSections($fs->pl),
            'revenue'   => self::section(
                $fs->pl[FsSectionCode::PL_OPERATING_REVENUE]
                    ?? $fs->pl[Section::CODE_REVENUE]
                    ?? null,
            ),
            'expenses'  => self::section($fs->pl[Section::CODE_EXPENSES] ?? null),
            'netIncome' => $fs->totals['net_income'] ?? '0.0000',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeCs(FinancialStatement $fs): array
    {
        return [
            'sections'  => self::orderedSections($fs->cs),
            'operating' => self::section($fs->cs[Section::CODE_OPERATING_CF] ?? null),
            'investing' => self::section($fs->cs[Section::CODE_INVESTING_CF] ?? null),
            'financing' => self::section($fs->cs[Section::CODE_FINANCING_CF] ?? null),
            'netChange' => $fs->cs[Section::CODE_OPERATING_CF]->subtotal ?? '0.0000',
        ];
    }

    /**
     * Emit every section in the map as a flat list, ordered by sortOrder
     * ascending. Clients reassemble the tree via the `parentCode` field.
     *
     * @param array<string, Section> $sections
     * @return list<array<string, mixed>>
     */
    private static function orderedSections(array $sections): array
    {
        $values = array_values($sections);
        usort($values, static function (Section $a, Section $b): int {
            if ($a->sortOrder !== $b->sortOrder) {
                return $a->sortOrder <=> $b->sortOrder;
            }
            return strcmp($a->code, $b->code);
        });
        return array_map(
            static fn (Section $s): array => [
                'code'       => $s->code,
                'parentCode' => $s->parentCode,
                'label'      => $s->label,
                'sortOrder'  => $s->sortOrder,
                'isSubtotal' => $s->isSubtotal,
                'isTotal'    => $s->isTotal,
                'subtotal'   => $s->subtotal,
                'lines'      => array_map(
                    static fn (FinancialStatementLine $line): array => [
                        'accountTitleId' => $line->accountTitleId,
                        'accountCode'    => $line->accountTitleCode,
                        'label'          => $line->label,
                        'amount'         => $line->amount,
                        'depth'          => $line->depth,
                        'isSubtotal'     => $line->isSubtotal,
                    ],
                    $s->lines,
                ),
            ],
            $values,
        );
    }

    /**
     * @return array<string, string>
     */
    private static function bsTotals(FinancialStatement $fs): array
    {
        $assetTotal = $fs->bs[FsSectionCode::BS_ASSET_TOTAL]->subtotal
            ?? $fs->bs[FsSectionCode::BS_ASSET]->subtotal
            ?? $fs->bs[Section::CODE_ASSETS]->subtotal
            ?? '0.0000';
        $liabilityTotal = $fs->bs[FsSectionCode::BS_LIABILITY_TOTAL]->subtotal
            ?? $fs->bs[FsSectionCode::BS_LIABILITY]->subtotal
            ?? $fs->bs[Section::CODE_LIABILITIES]->subtotal
            ?? '0.0000';
        $equityTotal = $fs->bs[FsSectionCode::BS_EQUITY_TOTAL]->subtotal
            ?? $fs->bs[FsSectionCode::BS_EQUITY]->subtotal
            ?? $fs->bs[Section::CODE_EQUITY]->subtotal
            ?? '0.0000';
        return [
            'assets'      => $assetTotal,
            'liabilities' => $liabilityTotal,
            'equity'      => $equityTotal,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function section(?Section $section): array
    {
        if ($section === null) {
            return [
                'title'    => '',
                'lines'    => [],
                'subtotal' => '0.0000',
            ];
        }
        return [
            'title'    => $section->label,
            'lines'    => array_map(
                static fn (FinancialStatementLine $line): array => [
                    'accountTitleId' => $line->accountTitleId,
                    'accountCode'    => $line->accountTitleCode,
                    'label'          => $line->label,
                    'amount'         => $line->amount,
                    'depth'          => $line->depth,
                    'isSubtotal'     => $line->isSubtotal,
                ],
                $section->lines,
            ),
            'subtotal' => $section->subtotal,
        ];
    }
}
