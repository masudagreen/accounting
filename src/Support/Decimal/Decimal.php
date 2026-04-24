<?php

declare(strict_types=1);

namespace Rucaro\Support\Decimal;

use InvalidArgumentException;

/**
 * Tiny fixed-scale decimal helper used where the full `bcmath` extension is
 * not guaranteed to be present (e.g. stock `php:8.3-cli` containers used in CI).
 *
 * Delegates to `bcadd` / `bccomp` when the extension is loaded for strict
 * correctness on arbitrary-precision inputs; otherwise falls back to a
 * fixed-point `int64` implementation scaled by `10^scale`, which is
 * sufficient for DECIMAL(18, 4) accounting values (|amount| <= 10^14).
 *
 * All inputs MUST match the decimal pattern `^-?\d+(\.\d+)?$` — callers that
 * read user input are expected to validate before routing here.
 */
final class Decimal
{
    public const SCALE = 4;

    /**
     * Add two scale-4 decimals, returning a scale-4 string.
     */
    public static function add(string $a, string $b): string
    {
        if (function_exists('bcadd')) {
            /** @var string */
            return bcadd($a, $b, self::SCALE);
        }
        $ai = self::toScaledInt($a, self::SCALE);
        $bi = self::toScaledInt($b, self::SCALE);
        return self::fromScaledInt($ai + $bi, self::SCALE);
    }

    /**
     * Compare `$a` vs `$b` at scale 4: -1 / 0 / +1.
     */
    public static function compare(string $a, string $b): int
    {
        if (function_exists('bccomp')) {
            return bccomp($a, $b, self::SCALE);
        }
        $ai = self::toScaledInt($a, self::SCALE);
        $bi = self::toScaledInt($b, self::SCALE);
        return $ai <=> $bi;
    }

    /**
     * Normalize to "ddd.dddd" form with the configured scale.
     */
    public static function normalize(string $v): string
    {
        if (function_exists('bcadd')) {
            /** @var string */
            return bcadd($v, '0', self::SCALE);
        }
        return self::fromScaledInt(self::toScaledInt($v, self::SCALE), self::SCALE);
    }

    private static function toScaledInt(string $v, int $scale): int
    {
        if (!preg_match('/^-?\d+(\.\d+)?$/', $v)) {
            throw new InvalidArgumentException(sprintf('Invalid decimal: %s', $v));
        }
        $negative = str_starts_with($v, '-');
        $abs = ltrim($v, '-');
        $dot = strpos($abs, '.');
        if ($dot === false) {
            $intPart = $abs;
            $fracPart = '';
        } else {
            $intPart = substr($abs, 0, $dot);
            $fracPart = substr($abs, $dot + 1);
        }
        $fracPart = substr(str_pad($fracPart, $scale, '0'), 0, $scale);

        $combined = $intPart . $fracPart;
        // Guard against int overflow at our domain scale (DECIMAL(18,4) fits
        // comfortably below PHP_INT_MAX on 64-bit runtimes — we only run PHP
        // 8.3 in tested contexts).
        if (PHP_INT_SIZE < 8) {
            throw new \RuntimeException('Decimal fallback requires 64-bit PHP.');
        }
        /** @var int $n */
        $n = (int) $combined;
        return $negative ? -$n : $n;
    }

    private static function fromScaledInt(int $n, int $scale): string
    {
        $negative = $n < 0;
        $abs = (string) ($negative ? -$n : $n);
        $abs = str_pad($abs, $scale + 1, '0', STR_PAD_LEFT);
        $intPart = substr($abs, 0, strlen($abs) - $scale);
        $fracPart = substr($abs, strlen($abs) - $scale);
        $result = $intPart . '.' . $fracPart;
        return $negative ? '-' . $result : $result;
    }
}
