<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Service;

use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMapping;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\FsSectionCode;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Support\Decimal\Decimal;

/**
 * Pure domain service that assembles Section[] from:
 *   - a trial balance (per-account balances)
 *   - account_title_fs_mappings (account → FS section code + sign)
 *   - fs_section_definitions (hierarchical BS / PL skeleton)
 *
 * Ports the calculation loop that {@see CalcAccountTitleFS::_loopVarsCalc}
 * and {@see CalcAccountTitleFS::_getValueFS} performed in the legacy code:
 *
 *   1. For each mapping, look up the trial-balance row for its account and
 *      fold (balance * sign) into the section's line list + subtotal.
 *   2. For each section definition with a `formula` (e.g. gross_profit =
 *      +operating_revenue - cost_of_sales), compute the subtotal from sibling
 *      sections rather than from mappings.
 *   3. For each non-subtotal section whose subtotal is the sum of its child
 *      sections (via parent_code), roll the children up.
 *
 * Output is a flat `array<sectionCode, Section>` keyed by section code — the
 * renderer (Smarty) walks the `fs_section_definitions` tree to lay out the
 * output and reads this map by code.
 *
 * Pure: no DB access, no I/O. Testable in isolation.
 */
final class FinancialStatementBuilder
{
    /**
     * Assemble sections for the given `FsKind`.
     *
     * @param list<TrialBalanceRow>        $rows
     * @param list<AccountTitleFsMapping>  $mappings
     * @param list<FsSectionDefinition>    $definitions
     * @return array<string, Section>
     */
    public function build(FsKind $kind, array $rows, array $mappings, array $definitions): array
    {
        $rowsByAccount = self::indexRows($rows);
        $defsByCode = self::indexDefinitions($definitions, $kind);

        // 1. Fold mappings into leaf sections first.
        $accumulator = self::foldMappings($kind, $rowsByAccount, $mappings, $defsByCode);

        // 2. Roll child sections up into their parents (parent_code).
        self::rollUpChildren($accumulator, $defsByCode);

        // 3. Compute formula-driven subtotals (gross_profit, operating_income,
        //    ordinary_income, pretax_income, net_income, asset_total, ...).
        self::computeFormulaSubtotals($accumulator, $defsByCode);

        // Materialise the final Section map.
        return self::materialise($accumulator, $defsByCode);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * @param list<TrialBalanceRow> $rows
     * @return array<string, TrialBalanceRow>
     */
    private static function indexRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $out[$row->accountTitleId] = $row;
        }
        return $out;
    }

    /**
     * @param list<FsSectionDefinition> $definitions
     * @return array<string, FsSectionDefinition>
     */
    private static function indexDefinitions(array $definitions, FsKind $kind): array
    {
        $out = [];
        foreach ($definitions as $d) {
            if ($d->kind !== $kind) {
                continue;
            }
            $out[$d->code] = $d;
        }
        return $out;
    }

    /**
     * Accumulator shape: ['lines' => list<FinancialStatementLine>, 'subtotal' => string]
     *
     * @param array<string, TrialBalanceRow>      $rowsByAccount
     * @param list<AccountTitleFsMapping>         $mappings
     * @param array<string, FsSectionDefinition>  $defsByCode
     * @return array<string, array{lines: list<FinancialStatementLine>, subtotal: string}>
     */
    private static function foldMappings(
        FsKind $kind,
        array $rowsByAccount,
        array $mappings,
        array $defsByCode,
    ): array {
        /** @var array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc */
        $acc = [];
        foreach (array_keys($defsByCode) as $code) {
            $acc[$code] = ['lines' => [], 'subtotal' => '0.0000'];
        }

        // Sort mappings so lines within a section are ordered deterministically.
        usort($mappings, static function (AccountTitleFsMapping $a, AccountTitleFsMapping $b): int {
            if ($a->sectionCode !== $b->sectionCode) {
                return $a->sectionCode <=> $b->sectionCode;
            }
            if ($a->sortOrder !== $b->sortOrder) {
                return $a->sortOrder <=> $b->sortOrder;
            }
            return $a->accountTitleId <=> $b->accountTitleId;
        });

        foreach ($mappings as $m) {
            if ($m->kind !== $kind) {
                continue;
            }
            if (!isset($acc[$m->sectionCode])) {
                // Unknown section code — skip rather than crash. Callers can
                // surface a warning via a separate lint step.
                continue;
            }
            $row = $rowsByAccount[$m->accountTitleId] ?? null;
            if ($row === null) {
                continue;
            }
            $signed = $m->sign === -1
                ? self::negate($row->balance)
                : Decimal::normalize($row->balance);

            $label = $m->displayLabel ?? ($row->accountTitleName !== '' ? $row->accountTitleName : $row->accountTitleCode);
            $line = FinancialStatementLine::ofAccount(
                accountTitleId: $row->accountTitleId,
                accountTitleCode: $row->accountTitleCode,
                label: $label,
                amount: $signed,
                depth: 2,
            );
            $acc[$m->sectionCode]['lines'][] = $line;
            $acc[$m->sectionCode]['subtotal'] = Decimal::add($acc[$m->sectionCode]['subtotal'], $signed);
        }

        return $acc;
    }

    /**
     * Accumulator is mutated: parents receive the subtotal of each child whose
     * parent_code points at them. Runs in sort_order ascending so children are
     * summarised before their parents are themselves consumed.
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, FsSectionDefinition>                                          $defsByCode
     */
    private static function rollUpChildren(array &$acc, array $defsByCode): void
    {
        // Process definitions from deepest first (max parent chain length)
        // so leaves roll into intermediate parents before the intermediate
        // parents themselves roll into the root ("asset" etc.).
        $depth = [];
        foreach (array_keys($defsByCode) as $code) {
            $depth[$code] = self::depthOf($code, $defsByCode);
        }
        $codesByDepthDesc = array_keys($defsByCode);
        usort($codesByDepthDesc, static fn (string $a, string $b): int => $depth[$b] - $depth[$a]);

        foreach ($codesByDepthDesc as $childCode) {
            $def = $defsByCode[$childCode];
            if ($def->parentCode === null) {
                continue;
            }
            if (!isset($acc[$def->parentCode])) {
                continue;
            }
            // Roll this child's subtotal into its parent.
            $acc[$def->parentCode]['subtotal'] = Decimal::add(
                $acc[$def->parentCode]['subtotal'],
                $acc[$childCode]['subtotal'],
            );
        }
    }

    /**
     * Count of parent links above `$code` (0 for roots).
     *
     * @param array<string, FsSectionDefinition> $defsByCode
     */
    private static function depthOf(string $code, array $defsByCode): int
    {
        $d = 0;
        $cursor = $code;
        while (isset($defsByCode[$cursor]) && $defsByCode[$cursor]->parentCode !== null) {
            $d++;
            $cursor = $defsByCode[$cursor]->parentCode;
            if ($d > 32) {
                // Cycle guard.
                break;
            }
        }
        return $d;
    }

    /**
     * For sections carrying an explicit formula (e.g. gross_profit), compute
     * the subtotal from referenced codes. Ports the legacy `flagCalc = sum/net`
     * logic: each reference uses +/- to indicate sign.
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, FsSectionDefinition>                                          $defsByCode
     */
    private static function computeFormulaSubtotals(array &$acc, array $defsByCode): void
    {
        // Process in definition `sort_order` ascending so early subtotals
        // (gross_profit) are ready when late subtotals (operating_income)
        // reference them.
        $ordered = $defsByCode;
        uasort(
            $ordered,
            static fn (FsSectionDefinition $a, FsSectionDefinition $b): int => $a->sortOrder <=> $b->sortOrder,
        );
        foreach ($ordered as $code => $def) {
            if ($def->formula === null || $def->formula === '') {
                continue;
            }
            $sum = '0.0000';
            foreach ($def->parsedFormula() as [$sign, $ref]) {
                $refValue = $acc[$ref]['subtotal'] ?? '0.0000';
                if ($sign === -1) {
                    $refValue = self::negate($refValue);
                }
                $sum = Decimal::add($sum, $refValue);
            }
            $acc[$code]['subtotal'] = Decimal::normalize($sum);
        }
    }

    /**
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, FsSectionDefinition>                                          $defsByCode
     * @return array<string, Section>
     */
    private static function materialise(array $acc, array $defsByCode): array
    {
        $sections = [];
        foreach ($defsByCode as $code => $def) {
            $bucket = $acc[$code] ?? ['lines' => [], 'subtotal' => '0.0000'];
            $sections[$code] = new Section(
                code: $code,
                label: $def->label,
                lines: $bucket['lines'],
                subtotal: Decimal::normalize($bucket['subtotal']),
                parentCode: $def->parentCode,
                sortOrder: $def->sortOrder,
                isSubtotal: $def->isSubtotal,
                isTotal: $def->isTotal,
            );
        }
        return $sections;
    }

    /**
     * Fold the current-period net income from the PL into the BS equity side
     * under the `retained_earnings` section as an explicit line labelled
     * "当期純利益" (current-period profit). All parent sections
     * (shareholders_equity, equity) and totals that depend on equity
     * (equity_total, liability_equity_total) are recomputed so that the BS
     * balances asset_total = liability_total + equity_total.
     *
     * Returns a fresh BS map with the new line injected. Inputs are not
     * mutated.
     *
     * When either the BS or the PL is empty (e.g. the caller asked for PL
     * only) the BS is returned untouched.
     *
     * @param array<string, Section> $bs
     * @param array<string, Section> $pl
     * @return array<string, Section>
     */
    public function applyNetIncomeCarryOver(array $bs, array $pl): array
    {
        if ($bs === [] || $pl === []) {
            return $bs;
        }
        $retained = $bs[FsSectionCode::BS_RETAINED_EARNINGS] ?? null;
        $netIncomeSection = $pl[FsSectionCode::PL_NET_INCOME] ?? null;
        if ($retained === null || $netIncomeSection === null) {
            return $bs;
        }

        $netIncome = Decimal::normalize($netIncomeSection->subtotal);

        $profitLine = FinancialStatementLine::ofAccount(
            accountTitleId: '__net_income_current_period',
            accountTitleCode: '__ni',
            label: '当期純利益',
            amount: $netIncome,
            depth: 2,
        );
        $newRetained = $retained->withAppendedLine($profitLine);

        $next = $bs;
        $next[FsSectionCode::BS_RETAINED_EARNINGS] = $newRetained;

        // Recompute equity chain: shareholders_equity = sum of its children,
        // equity = sum of its children, equity_total = formula +equity,
        // liability_equity_total = +liability +equity.
        self::rebuildSubtotalFromChildren($next, FsSectionCode::BS_SHAREHOLDERS_EQUITY);
        self::rebuildSubtotalFromChildren($next, FsSectionCode::BS_EQUITY);

        if (isset($next[FsSectionCode::BS_EQUITY_TOTAL])) {
            $next[FsSectionCode::BS_EQUITY_TOTAL] = $next[FsSectionCode::BS_EQUITY_TOTAL]
                ->withSubtotal($next[FsSectionCode::BS_EQUITY]->subtotal);
        }
        if (isset($next[FsSectionCode::BS_LIABILITY_EQUITY_TOTAL])) {
            $liab = $next[FsSectionCode::BS_LIABILITY]->subtotal ?? '0.0000';
            $eq = $next[FsSectionCode::BS_EQUITY]->subtotal;
            $next[FsSectionCode::BS_LIABILITY_EQUITY_TOTAL] = $next[FsSectionCode::BS_LIABILITY_EQUITY_TOTAL]
                ->withSubtotal(Decimal::add($liab, $eq));
        }

        return $next;
    }

    /**
     * Assert the BS balances — asset_total === liability_total + equity_total.
     * Uses `liability_equity_total` when available, otherwise reconstructs from
     * `liability_total + equity_total`. Throws on mismatch so the caller can
     * surface a 500 rather than silently rendering an unbalanced statement.
     *
     * No-op when the BS is empty (kind=PL only).
     *
     * @param array<string, Section> $bs
     */
    public function assertBalanced(array $bs): void
    {
        if ($bs === []) {
            return;
        }
        $assetTotal = $bs[FsSectionCode::BS_ASSET_TOTAL]->subtotal
            ?? $bs[FsSectionCode::BS_ASSET]->subtotal
            ?? null;
        if ($assetTotal === null) {
            return;
        }

        $liabilityEquity = $bs[FsSectionCode::BS_LIABILITY_EQUITY_TOTAL]->subtotal ?? null;
        if ($liabilityEquity === null) {
            $liab = $bs[FsSectionCode::BS_LIABILITY_TOTAL]->subtotal
                ?? $bs[FsSectionCode::BS_LIABILITY]->subtotal
                ?? '0.0000';
            $eq = $bs[FsSectionCode::BS_EQUITY_TOTAL]->subtotal
                ?? $bs[FsSectionCode::BS_EQUITY]->subtotal
                ?? '0.0000';
            $liabilityEquity = Decimal::add($liab, $eq);
        }

        if (Decimal::compare($assetTotal, $liabilityEquity) !== 0) {
            throw InvariantViolationException::for(
                'financial_statement.bs_must_balance',
                [
                    'asset_total'            => $assetTotal,
                    'liability_equity_total' => $liabilityEquity,
                    'delta'                  => self::subtract($assetTotal, $liabilityEquity),
                ],
            );
        }
    }

    /**
     * Recompute `$code`'s subtotal from the direct children whose parent_code
     * references it. Lines on the section itself are preserved (J-GAAP keeps
     * direct account lines under `shareholders_equity` even though it also
     * has child sections like `capital` / `retained_earnings`).
     *
     * @param array<string, Section> $sections
     */
    private static function rebuildSubtotalFromChildren(array &$sections, string $code): void
    {
        if (!isset($sections[$code])) {
            return;
        }
        // Base = sum of raw lines directly attached to this section.
        $sum = '0.0000';
        foreach ($sections[$code]->lines as $line) {
            if ($line->isSubtotal) {
                continue;
            }
            $sum = Decimal::add($sum, $line->amount);
        }
        // Add each child subtotal.
        foreach ($sections as $other) {
            if ($other->parentCode === $code) {
                $sum = Decimal::add($sum, $other->subtotal);
            }
        }
        $sections[$code] = $sections[$code]->withSubtotal($sum);
    }

    private static function subtract(string $a, string $b): string
    {
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . Decimal::normalize($b));
        return Decimal::add($a, $negated);
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
