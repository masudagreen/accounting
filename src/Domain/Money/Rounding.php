<?php

declare(strict_types=1);

namespace App\Domain\Money;

/**
 * 端数処理ポリシー。
 *
 * 元実装 `back/class/else/lib/Display.php::getNumDisplay` の挙動を再現:
 * - 入力に小数点が含まれない → 入力をそのまま返す
 * - numLevel == 0 → 整数化 (ceil/floor/round)
 * - numLevel >  0 → その桁で処理 (10^numLevel を掛けて round 後に戻す)
 * - 小数桁数が numLevel 以下なら何もしない
 *
 * 純関数。副作用なし。
 */
final class Rounding
{
    /**
     * @param int|float    $value
     * @return int|float
     */
    public static function apply(int|float $value, RoundingMode $mode, int $level): int|float
    {
        // 整数値ならそのまま
        if (is_int($value) || floor($value) === (float) $value) {
            // ただし呼び出し側が `float` で 0.0 を渡してきた場合の型保存
            if (is_int($value)) {
                return $value;
            }
            // 整数相当の float: 元実装は文字列に '.' が無いかでチェックしている。
            // PHP の (string) で 1.0 → "1" となるので元実装ではそのまま返す。
            return $value;
        }

        if ($level === 0) {
            return match ($mode) {
                RoundingMode::Floor => (int) floor($value),
                RoundingMode::Ceil  => (int) ceil($value),
                RoundingMode::Round => (int) round($value, 0, PHP_ROUND_HALF_UP),
            };
        }

        // numLevel > 0 で、小数桁数が numLevel を超える場合のみ処理
        $decimalPart = self::decimalString($value);
        if (strlen($decimalPart) <= $level) {
            return $value;
        }

        $scale = 10 ** $level;
        $scaled = $value * $scale;

        $rounded = match ($mode) {
            RoundingMode::Floor => floor($scaled),
            RoundingMode::Ceil  => ceil($scaled),
            RoundingMode::Round => round($scaled, 0, PHP_ROUND_HALF_UP),
        };

        return $rounded / $scale;
    }

    /**
     * 値の小数部の文字列を返す ("1.235" → "235")。
     * 整数 / 整数相当値の場合は空文字。
     */
    private static function decimalString(int|float $value): string
    {
        $str = (string) $value;
        $pos = strpos($str, '.');
        if ($pos === false) {
            return '';
        }
        return substr($str, $pos + 1);
    }
}
