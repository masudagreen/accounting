<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

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
        /** @var array<string, mixed> $parsed */
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
     * @return list<array{side: string, account_title_id: string, sub_account_title_id: ?string, amount: string, memo: string}>
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
            $side    = self::str($raw, 'side', '');
            $account = self::str($raw, 'account_title_id', '');
            $sub     = self::str($raw, 'sub_account_title_id', '');
            $amount  = self::str($raw, 'amount', '');
            $memo    = self::str($raw, 'memo', '');

            if ($side === '' && $account === '' && $amount === '' && $memo === '') {
                continue; // empty placeholder row
            }
            $out[] = [
                'side'                 => $side,
                'account_title_id'     => $account,
                'sub_account_title_id' => $sub === '' ? null : $sub,
                'amount'               => $amount,
                'memo'                 => $memo,
            ];
        }
        return $out;
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
            return $cleaned . '.0000';
        }
        [$int, $frac] = explode('.', $cleaned, 2);
        $frac = substr(str_pad($frac, 4, '0'), 0, 4);
        return $int . '.' . $frac;
    }
}
