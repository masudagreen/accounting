<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Planning;

use Rucaro\Http\ServerRequest;

/**
 * Form-parsing helpers shared across the planning CRUD controllers.
 *
 * Keeps amount-normalisation and nested-array extraction logic in one place
 * so each controller stays focused on its orchestration.
 */
final class PlanningFormSupport
{
    /**
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
     * @param array<string, mixed> $bag
     */
    public static function nullableStr(array $bag, string $key): ?string
    {
        $v = $bag[$key] ?? null;
        if (!is_string($v)) {
            return null;
        }
        $trimmed = trim($v);
        return $trimmed === '' ? null : $trimmed;
    }

    public static function bool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            $lc = strtolower(trim($value));
            return $lc === '1' || $lc === 'true' || $lc === 'on' || $lc === 'yes';
        }
        if (is_int($value)) {
            return $value !== 0;
        }
        return false;
    }

    /**
     * Normalise an operator-entered amount into a scale-4 decimal string.
     * Returns "0.0000" for empty input and leaves malformed values to be
     * rejected downstream by the domain invariant layer.
     */
    public static function normalizeAmount(string $raw): string
    {
        $cleaned = str_replace([',', ' ', "\u{3000}"], '', trim($raw));
        if ($cleaned === '') {
            return '0.0000';
        }
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cleaned)) {
            return $cleaned;
        }
        if (!str_contains($cleaned, '.')) {
            return $cleaned . '.0000';
        }
        [$int, $frac] = explode('.', $cleaned, 2);
        $frac = substr(str_pad($frac, 4, '0'), 0, 4);
        return $int . '.' . $frac;
    }

    /**
     * Extract a `rows[N][key]` nested form payload into a list of row maps.
     *
     * @param array<string, mixed> $bag
     * @param list<string> $columns
     * @return list<array<string, string>>
     */
    public static function extractRows(array $bag, string $groupKey, array $columns): array
    {
        $raw = $bag[$groupKey] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $mapped = [];
            $hasValue = false;
            foreach ($columns as $col) {
                $v = $row[$col] ?? null;
                $mapped[$col] = is_string($v) ? trim($v) : '';
                if ($mapped[$col] !== '') {
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                $out[] = $mapped;
            }
        }
        return $out;
    }

    /**
     * Build a 12-month amount array from `monthly[N]` indexed form inputs.
     *
     * @param array<string, mixed> $bag
     * @return list<string>
     */
    public static function extractMonthly(array $bag, string $groupKey): array
    {
        $raw = $bag[$groupKey] ?? null;
        /** @var list<string> $out */
        $out = [];
        for ($i = 0; $i < 12; $i++) {
            $v = is_array($raw) ? ($raw[$i] ?? null) : null;
            $out[] = self::normalizeAmount(is_string($v) ? $v : '');
        }
        return $out;
    }

    /**
     * Pull a list of nested rows where each has 12 monthly amounts.
     *
     * @param array<string, mixed> $bag
     * @return list<array{label: string, category: string, monthly: list<string>, memo: string}>
     */
    public static function extractMonthlyRows(array $bag, string $groupKey): array
    {
        $raw = $bag[$groupKey] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $label    = self::str($row, 'label');
            $category = self::str($row, 'category');
            $memo     = self::str($row, 'memo');
            $monthlyRaw = $row['monthly'] ?? null;
            /** @var list<string> $monthly */
            $monthly = [];
            $hasAmount = false;
            for ($i = 0; $i < 12; $i++) {
                $v = is_array($monthlyRaw) ? ($monthlyRaw[$i] ?? null) : null;
                $m = self::normalizeAmount(is_string($v) ? $v : '');
                $monthly[] = $m;
                if ($m !== '0.0000') {
                    $hasAmount = true;
                }
            }
            if ($label === '' && $category === '' && !$hasAmount && $memo === '') {
                continue;
            }
            $out[] = [
                'label'    => $label,
                'category' => $category,
                'monthly'  => $monthly,
                'memo'     => $memo,
            ];
        }
        return $out;
    }
}
