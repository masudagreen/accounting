<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs\Service;

use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMapping;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionCode;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Support\Decimal\Decimal;

/**
 * Pure domain service that assembles the J-GAAP indirect-method Cash Flow
 * Statement as a `array<sectionCode, Section>`.
 *
 * Inputs:
 *   - `periodRows`       : trial-balance rows for the current period
 *                          (movement — i.e. debit - credit totals on normalSide)
 *   - `priorRows`        : trial-balance rows for the prior period (optional;
 *                          used only if we later want balance-change deltas;
 *                          present for API symmetry — currently unused by
 *                          the simple movement-based model)
 *   - `mappings`         : {@see AccountTitleCsMapping} rows for the entity
 *   - `definitions`      : {@see CsSectionDefinition} seed
 *   - `pretaxIncome`     : scale-4 decimal string (fed in from the PL builder)
 *   - `beginningCash`    : scale-4 decimal string (prior-period ending cash)
 *
 * Outputs a flat `array<sectionCode, Section>` keyed by CS section code so
 * templates can render by code rather than walking the tree.
 *
 * Ports {@see Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleFSCS::_loopVarsCalc}
 * and `_getValueFS` from the legacy codebase. The legacy implementation:
 *   1. Looped accounts tagged with `varsJgaapCS.varsInDirect` (indirect method).
 *   2. Split each into Plus / Minus legs by `idAccountTitleMinus`/`Plus`.
 *   3. Summed `sumNext` (or `sumDebit` / `sumCredit` for non-net methods).
 *   4. Reverse-signed the Minus leg and rolled into `idAccountTitleCS` bucket.
 *   5. Walked the section tree with `flagCalc = sum / net` subtotals.
 *
 * The new model collapses those five passes into three:
 *   1. Fold every mapping into its leaf section as a signed line (working-capital
 *      mappings flip sign: an asset increase is a cash decrease).
 *   2. Attach the pretax income line under `operating_pretax_income` and the
 *      beginning cash line under `beginning_cash`.
 *   3. Roll children into their `parent_code` parents, then compute formula
 *      subtotals (operating_cf_subtotal, operating_cf_total, ..., ending_cash).
 *
 * Pure: no DB access, no I/O. Testable in isolation.
 */
final class CashFlowStatementBuilder
{
    /**
     * Input bundle describing one period's worth of CS inputs.
     *
     * Immutable container used by {@see self::build()} — keeps the method
     * signature small and lets callers add/remove optional inputs without a
     * cascading constructor explosion.
     *
     * @param list<TrialBalanceRow>      $periodRows
     * @param list<TrialBalanceRow>      $priorRows
     * @param list<AccountTitleCsMapping> $mappings
     * @param list<CsSectionDefinition>   $definitions
     * @return array<string, Section>
     */
    public function build(
        array $periodRows,
        array $priorRows,
        array $mappings,
        array $definitions,
        string $pretaxIncome,
        string $beginningCash,
    ): array {
        $rowsByAccount = self::indexRows($periodRows);
        $defsByCode = self::indexDefinitions($definitions);

        // 1. Fold mappings into their leaf sections.
        $acc = self::initAccumulator($defsByCode);
        self::foldMappings($acc, $rowsByAccount, $mappings);

        // 2. Seed pretax income and beginning cash lines — these come from
        //    outside the trial balance (PL builder + prior-period snapshot).
        self::seedPretaxIncome($acc, $defsByCode, $pretaxIncome);
        self::seedBeginningCash($acc, $defsByCode, $beginningCash);

        // 3. Roll children into their parents.
        self::rollUpChildren($acc, $defsByCode);

        // 4. Compute formula subtotals (operating_cf_subtotal, *_cf_total, ending_cash, ...).
        self::computeFormulaSubtotals($acc, $defsByCode);

        // Unused for now, but kept in the signature so future extensions can
        // implement balance-delta working capital without another rewrite.
        unset($priorRows);

        return self::materialise($acc, $defsByCode);
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
     * @param list<CsSectionDefinition> $definitions
     * @return array<string, CsSectionDefinition>
     */
    private static function indexDefinitions(array $definitions): array
    {
        $out = [];
        foreach ($definitions as $d) {
            $out[$d->code] = $d;
        }
        return $out;
    }

    /**
     * @param array<string, CsSectionDefinition> $defsByCode
     * @return array<string, array{lines: list<FinancialStatementLine>, subtotal: string}>
     */
    private static function initAccumulator(array $defsByCode): array
    {
        $acc = [];
        foreach (array_keys($defsByCode) as $code) {
            $acc[$code] = ['lines' => [], 'subtotal' => '0.0000'];
        }
        return $acc;
    }

    /**
     * Fold every mapping into its leaf section — signed amount, working-capital
     * flip, working-label derivation.
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, TrialBalanceRow>        $rowsByAccount
     * @param list<AccountTitleCsMapping>           $mappings
     */
    private static function foldMappings(array &$acc, array $rowsByAccount, array $mappings): void
    {
        // Deterministic order — (section, sort, accountId).
        usort($mappings, static function (AccountTitleCsMapping $a, AccountTitleCsMapping $b): int {
            if ($a->sectionCode !== $b->sectionCode) {
                return $a->sectionCode <=> $b->sectionCode;
            }
            if ($a->sortOrder !== $b->sortOrder) {
                return $a->sortOrder <=> $b->sortOrder;
            }
            return $a->accountTitleId <=> $b->accountTitleId;
        });

        foreach ($mappings as $m) {
            if (!isset($acc[$m->sectionCode])) {
                continue;
            }
            $row = $rowsByAccount[$m->accountTitleId] ?? null;
            if ($row === null) {
                continue;
            }

            $signed = self::applySign($row->balance, $m);

            $label = $m->displayLabel
                ?? ($row->accountTitleName !== '' ? $row->accountTitleName : $row->accountTitleCode);

            $line = FinancialStatementLine::ofAccount(
                accountTitleId: $row->accountTitleId,
                accountTitleCode: $row->accountTitleCode,
                label: $label,
                amount: $signed,
                depth: 2,
            );
            $acc[$m->sectionCode]['lines'][] = $line;
            $acc[$m->sectionCode]['subtotal'] = Decimal::add(
                $acc[$m->sectionCode]['subtotal'],
                $signed,
            );
        }
    }

    /**
     * Applied sign rules:
     *  - Working-capital flag flips the sign: an increase in receivables
     *    (cash debit) reduces operating CF, so we negate the raw balance.
     *  - Explicit sign (+1 / -1) multiplies on top of that.
     */
    private static function applySign(string $balance, AccountTitleCsMapping $m): string
    {
        $value = Decimal::normalize($balance);
        if ($m->isWorkingCapital) {
            $value = self::negate($value);
        }
        if ($m->sign === -1) {
            $value = self::negate($value);
        }
        return $value;
    }

    /**
     * Seed `operating_pretax_income` with the value coming from the PL.
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, CsSectionDefinition> $defsByCode
     */
    private static function seedPretaxIncome(array &$acc, array $defsByCode, string $pretaxIncome): void
    {
        $code = CsSectionCode::OPERATING_PRETAX_INCOME;
        if (!isset($defsByCode[$code])) {
            return;
        }
        $amount = Decimal::normalize($pretaxIncome);
        $line = new FinancialStatementLine(
            label: $defsByCode[$code]->label,
            amount: $amount,
            accountTitleId: null,
            accountTitleCode: null,
            depth: 2,
            isSubtotal: false,
        );
        $acc[$code]['lines'][] = $line;
        $acc[$code]['subtotal'] = Decimal::add($acc[$code]['subtotal'], $amount);
    }

    /**
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, CsSectionDefinition> $defsByCode
     */
    private static function seedBeginningCash(array &$acc, array $defsByCode, string $beginningCash): void
    {
        $code = CsSectionCode::BEGINNING_CASH;
        if (!isset($defsByCode[$code])) {
            return;
        }
        $amount = Decimal::normalize($beginningCash);
        $acc[$code]['subtotal'] = $amount;
    }

    /**
     * Roll each child section into its parent via `parentCode`, deepest first
     * so indirection through intermediate parents (operating_cf → operating_cf_subtotal
     * via formula) is respected.
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, CsSectionDefinition> $defsByCode
     */
    private static function rollUpChildren(array &$acc, array $defsByCode): void
    {
        $depth = [];
        foreach (array_keys($defsByCode) as $code) {
            $depth[$code] = self::depthOf($code, $defsByCode);
        }
        $codes = array_keys($defsByCode);
        usort($codes, static fn (string $a, string $b): int => $depth[$b] - $depth[$a]);

        foreach ($codes as $childCode) {
            $def = $defsByCode[$childCode];
            if ($def->parentCode === null) {
                continue;
            }
            if (!isset($acc[$def->parentCode])) {
                continue;
            }
            $acc[$def->parentCode]['subtotal'] = Decimal::add(
                $acc[$def->parentCode]['subtotal'],
                $acc[$childCode]['subtotal'],
            );
        }
    }

    /**
     * @param array<string, CsSectionDefinition> $defsByCode
     */
    private static function depthOf(string $code, array $defsByCode): int
    {
        $d = 0;
        $cursor = $code;
        while (isset($defsByCode[$cursor]) && $defsByCode[$cursor]->parentCode !== null) {
            $d++;
            $cursor = $defsByCode[$cursor]->parentCode;
            if ($d > 32) {
                break;
            }
        }
        return $d;
    }

    /**
     * Resolve formula-driven subtotals (`operating_cf_total`, `ending_cash`, ...).
     *
     * @param array<string, array{lines: list<FinancialStatementLine>, subtotal: string}> $acc
     * @param array<string, CsSectionDefinition> $defsByCode
     */
    private static function computeFormulaSubtotals(array &$acc, array $defsByCode): void
    {
        $ordered = $defsByCode;
        uasort(
            $ordered,
            static fn (CsSectionDefinition $a, CsSectionDefinition $b): int
                => $a->sortOrder <=> $b->sortOrder,
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
     * @param array<string, CsSectionDefinition> $defsByCode
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
