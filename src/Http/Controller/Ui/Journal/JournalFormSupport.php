<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Http\ServerRequest;

/**
 * Shared form-parsing helpers for the Journal UI controllers.
 *
 * Extracted from the four concrete controllers to keep each of them small
 * and to make validation behaviour unit-testable in one place.
 *
 * Nothing here should depend on session or CSRF state — those are owned by
 * the controllers themselves.
 */
final class JournalFormSupport
{
    /**
     * Parse a urlencoded form body into a nested array that mirrors PHP's
     * native `$_POST`: scalars stay scalars, `lines[N][side]` becomes a
     * 2-level array.
     *
     * @return array<string, mixed>
     */
    public static function parseForm(ServerRequest $request): array
    {
        $parsed = [];
        parse_str($request->rawBody, $parsed);

        /* @var array<string, mixed> $parsed */
        return $parsed;
    }

    /**
     * Best-effort string fetch against a parsed form bag. Missing or
     * non-string values come back as the given default.
     *
     * @param array<string, mixed> $bag
     */
    public static function str(array $bag, string $key, string $default = ''): string
    {
        $v = $bag[$key] ?? null;
        if (is_string($v)) {
            return trim($v);
        }

        return $default;
    }

    /**
     * Extract a list of line payloads from `lines[N][...]` form inputs. Empty
     * rows (all fields blank) are dropped so operators can leave spare rows
     * dangling in the UI without forcing a validation error.
     *
     * @param array<string, mixed> $bag
     *
     * @return list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}>
     */
    public static function extractLines(array $bag): array
    {
        $rawLines = $bag['lines'] ?? null;
        if (!is_array($rawLines)) {
            return [];
        }
        $out = [];
        foreach ($rawLines as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $side = self::str($raw, 'side', '');
            $account = self::str($raw, 'account_title_id', '');
            $sub = self::str($raw, 'sub_account_title_id', '');
            $amount = self::str($raw, 'amount', '');
            $memo = self::str($raw, 'memo', '');
            $taxRate = self::str($raw, 'tax_rate_percent', '');
            $taxAmt = self::str($raw, 'tax_amount', '');
            $isReducedRaw = $raw['is_tax_reduced'] ?? null;

            if ($side === '' && $account === '' && $amount === '' && $memo === '') {
                continue; // empty placeholder row (tax-only fields don't count)
            }
            $out[] = [
                'side' => $side,
                'account_title_id' => $account,
                'sub_account_title_id' => $sub === '' ? null : $sub,
                'amount' => $amount,
                'memo' => $memo,
                'tax_rate_percent' => $taxRate === '' ? '0.00' : $taxRate,
                'tax_amount' => $taxAmt === '' ? '0.0000' : $taxAmt,
                'is_tax_reduced' => self::truthyCheckbox($isReducedRaw),
            ];
        }

        return $out;
    }

    /**
     * HTML form checkboxes only post their `value` when checked. We accept
     * the common truthy spellings ("1", "on", "true") so the form is robust
     * against whatever value attribute the template happens to use.
     */
    private static function truthyCheckbox(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            $v = strtolower(trim($value));

            return $v === '1' || $v === 'on' || $v === 'true' || $v === 'yes';
        }
        if (is_int($value)) {
            return $value !== 0;
        }

        return false;
    }

    /**
     * Normalize an operator-entered amount string into the DECIMAL(18, 4)
     * string the domain expects. Accepts comma grouping and leading/trailing
     * spaces; returns `'0.0000'` for empty input so the invariant layer can
     * raise a meaningful error instead of a parse failure.
     */
    public static function normalizeAmount(string $raw): string
    {
        $cleaned = str_replace([',', ' ', "\u{3000}"], '', trim($raw));
        if ($cleaned === '') {
            return '0.0000';
        }
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cleaned)) {
            return $cleaned; // let downstream validation raise
        }
        if (!str_contains($cleaned, '.')) {
            return $cleaned.'.0000';
        }
        [$int, $frac] = array_pad(explode('.', $cleaned, 2), 2, '');
        $frac = substr(str_pad($frac, 4, '0'), 0, 4);

        return $int.'.'.$frac;
    }

    /**
     * Normalize an operator-entered tax-rate percentage into the DECIMAL(5, 2)
     * string the domain expects. Accepts "10", "10.0", "10.00" and friends.
     * Empty input collapses to the "no tax" sentinel '0.00'. Garbage is passed
     * through unchanged so downstream validation can raise a meaningful error.
     */
    public static function normalizeTaxRate(string $raw): string
    {
        $cleaned = str_replace([',', ' ', "\u{3000}"], '', trim($raw));
        if ($cleaned === '') {
            return '0.00';
        }
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cleaned)) {
            return $cleaned;
        }
        if (!str_contains($cleaned, '.')) {
            return $cleaned.'.00';
        }
        [$int, $frac] = array_pad(explode('.', $cleaned, 2), 2, '');
        $frac = substr(str_pad($frac, 2, '0'), 0, 2);

        return $int.'.'.$frac;
    }

    /**
     * Normalize an operator-entered tax amount into the DECIMAL(18, 4) string
     * the domain expects. Same semantics as {@see normalizeAmount()} — kept as
     * a separate method so the call sites (and future scale tweaks) stay
     * self-documenting.
     */
    public static function normalizeTaxAmount(string $raw): string
    {
        return self::normalizeAmount($raw);
    }

    /**
     * Convert a parsed raw form line (from {@see extractLines}) into the
     * {@see JournalLineInput} expected by the create / update use cases.
     *
     * Centralized so the new- and edit- controllers stay byte-identical and
     * so the tax / sub-account mapping is unit-testable in one place.
     *
     * @param array{
     *     side: string,
     *     account_title_id: string,
     *     sub_account_title_id: ?string,
     *     amount: string,
     *     memo: string,
     *     tax_rate_percent: string,
     *     tax_amount: string,
     *     is_tax_reduced: bool
     * } $raw
     */
    public static function toLineInput(array $raw): JournalLineInput
    {
        return new JournalLineInput(
            side: $raw['side'],
            accountTitleId: $raw['account_title_id'],
            subAccountTitleId: $raw['sub_account_title_id'],
            amount: self::normalizeAmount($raw['amount']),
            taxRatePercent: self::normalizeTaxRate($raw['tax_rate_percent']),
            taxAmount: self::normalizeTaxAmount($raw['tax_amount']),
            isTaxReduced: $raw['is_tax_reduced'],
            memo: $raw['memo'],
        );
    }

    /* --------------------------------------------------------------------
     * F-4: the journal form was reverted from the F-2 pair schema back to
     *      the flat `lines[N][...]` schema with two independent tables
     *      (credits left, debits right, each with its own row controls).
     *
     *      The pair-shaped extractor / grouper that lived here have been
     *      removed; controllers call extractLines() / toLineInput()
     *      directly. Per-line memo is preserved via `lines[N][memo]` and
     *      flows through unchanged.
     * ------------------------------------------------------------------ */

    /**
     * Split a flat list of raw lines into two side-segregated lists for
     * the credit/debit tables in the form template. Used so the renderer
     * can iterate "show me only credit lines" without sprinkling
     * `{if $line.side == 'credit'}` checks in templates.
     *
     * Always returns at least one row per side so a fresh form has a
     * blank starting line on each table.
     *
     * @param list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}> $lines
     *
     * @return array{credit: list<array{account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}>, debit: list<array{account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string, tax_rate_percent: string, tax_amount: string, is_tax_reduced: bool}>}
     */
    public static function splitLinesBySide(array $lines): array
    {
        $blank = [
            'account_title_id' => '',
            'sub_account_title_id' => null,
            'amount' => '',
            'memo' => '',
            'tax_rate_percent' => '0.00',
            'tax_amount' => '0.0000',
            'is_tax_reduced' => false,
        ];
        $credit = [];
        $debit = [];
        foreach ($lines as $l) {
            $row = [
                'account_title_id' => $l['account_title_id'],
                'sub_account_title_id' => $l['sub_account_title_id'],
                'amount' => $l['amount'],
                'memo' => $l['memo'],
                'tax_rate_percent' => $l['tax_rate_percent'],
                'tax_amount' => $l['tax_amount'],
                'is_tax_reduced' => $l['is_tax_reduced'],
            ];
            if ($l['side'] === 'credit') {
                $credit[] = $row;
            } elseif ($l['side'] === 'debit') {
                $debit[] = $row;
            }
        }
        if ($credit === []) {
            $credit[] = $blank;
        }
        if ($debit === []) {
            $debit[] = $blank;
        }

        return ['credit' => $credit, 'debit' => $debit];
    }
}
