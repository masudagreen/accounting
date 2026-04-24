<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Master;

use Rucaro\Http\ServerRequest;

/**
 * Shared form-parsing helpers for the Master UI controllers.
 *
 * Extracted so every Master controller can parse POST bodies the same way,
 * matching the pattern already used by {@see \Rucaro\Http\Controller\Ui\Journal\JournalFormSupport}.
 */
final class MasterFormSupport
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
    public static function int(array $bag, string $key, int $default = 0): int
    {
        $v = $bag[$key] ?? null;
        if (is_int($v)) {
            return $v;
        }
        if (is_string($v) && preg_match('/^-?\d+$/', trim($v))) {
            return (int) trim($v);
        }
        return $default;
    }

    /**
     * Checkboxes post only when checked — the absence of a key is "false".
     *
     * @param array<string, mixed> $bag
     */
    public static function bool(array $bag, string $key, bool $default = false): bool
    {
        if (!array_key_exists($key, $bag)) {
            return $default;
        }
        $v = $bag[$key];
        if (is_bool($v)) {
            return $v;
        }
        if (is_string($v)) {
            $lc = strtolower(trim($v));
            if ($lc === '' || $lc === '0' || $lc === 'false' || $lc === 'no' || $lc === 'off') {
                return false;
            }
            return true;
        }
        if (is_int($v)) {
            return $v !== 0;
        }
        return $default;
    }

    /**
     * Normalise a nullable form string: empty string becomes null.
     */
    public static function optionalStr(string $raw): ?string
    {
        $trim = trim($raw);
        return $trim === '' ? null : $trim;
    }
}
