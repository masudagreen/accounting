<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Money;

use App\Domain\Money\Rounding;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 端数処理の仕様。元実装 `Code_Else_Lib_Display::getNumDisplay` の挙動を仕様として固定する。
 *
 * 元実装の挙動:
 * - 入力に小数点が含まれない → 入力をそのまま返す
 * - numLevel == 0 → 整数化 (ceil/floor/round)
 * - numLevel >  0 → その小数桁数で処理 (10^numLevel を掛けて round 後に戻す)
 * - 小数桁数が numLevel 以下なら何もしない (元実装の if(numStr > numLevel) 分岐)
 */
#[CoversClass(Rounding::class)]
final class RoundingTest extends TestCase
{
    #[Test]
    public function 入力に小数点がなければそのまま返す(): void
    {
        self::assertSame(100, Rounding::apply(100, RoundingMode::Floor, 0));
        self::assertSame(-50, Rounding::apply(-50, RoundingMode::Ceil, 2));
        self::assertSame(0, Rounding::apply(0, RoundingMode::Round, 5));
    }

    /** @return iterable<string, array{float, RoundingMode, int, int|float}> */
    public static function integerLevelCases(): iterable
    {
        // [入力, モード, numLevel=0, 期待値]
        yield 'floor 1.9 → 1'  => [1.9, RoundingMode::Floor, 0, 1];
        yield 'floor 1.1 → 1'  => [1.1, RoundingMode::Floor, 0, 1];
        yield 'ceil  1.1 → 2'  => [1.1, RoundingMode::Ceil, 0, 2];
        yield 'ceil  1.9 → 2'  => [1.9, RoundingMode::Ceil, 0, 2];
        yield 'round 1.4 → 1'  => [1.4, RoundingMode::Round, 0, 1];
        yield 'round 1.5 → 2'  => [1.5, RoundingMode::Round, 0, 2];
        yield 'round 2.5 → 3'  => [2.5, RoundingMode::Round, 0, 3]; // PHP round() は HALF_AWAY_FROM_ZERO
    }

    #[Test]
    #[DataProvider('integerLevelCases')]
    public function numLevel0_は整数化する(float $input, RoundingMode $mode, int $level, int|float $expected): void
    {
        self::assertSame($expected, Rounding::apply($input, $mode, $level));
    }

    /** @return iterable<string, array{float, RoundingMode, int, float}> */
    public static function decimalLevelCases(): iterable
    {
        yield 'floor 1.235 / 2 → 1.23' => [1.235, RoundingMode::Floor, 2, 1.23];
        yield 'ceil  1.231 / 2 → 1.24' => [1.231, RoundingMode::Ceil, 2, 1.24];
        yield 'round 1.235 / 2 → 1.24' => [1.235, RoundingMode::Round, 2, 1.24];
        yield 'round 1.234 / 2 → 1.23' => [1.234, RoundingMode::Round, 2, 1.23];
        yield 'floor 1.999 / 1 → 1.9'  => [1.999, RoundingMode::Floor, 1, 1.9];
    }

    #[Test]
    #[DataProvider('decimalLevelCases')]
    public function numLevel_n_はその桁数で処理する(float $input, RoundingMode $mode, int $level, float $expected): void
    {
        self::assertSame($expected, Rounding::apply($input, $mode, $level));
    }

    #[Test]
    public function 小数桁数がnumLevel以下なら何もしない(): void
    {
        // 元実装は if(numStr > numLevel) でしか丸めをかけない
        self::assertSame(1.2, Rounding::apply(1.2, RoundingMode::Round, 2));
        self::assertSame(1.23, Rounding::apply(1.23, RoundingMode::Round, 2));
    }

    /** @return iterable<string, array{float, RoundingMode, int, int|float}> */
    public static function negativeNumberCases(): iterable
    {
        // 負数の挙動: PHP の ceil/floor/round に従う
        yield 'floor -1.5 → -2' => [-1.5, RoundingMode::Floor, 0, -2];
        yield 'ceil  -1.5 → -1' => [-1.5, RoundingMode::Ceil, 0, -1];
        yield 'round -1.5 → -2' => [-1.5, RoundingMode::Round, 0, -2]; // HALF_AWAY_FROM_ZERO
        yield 'round -2.5 → -3' => [-2.5, RoundingMode::Round, 0, -3];
    }

    #[Test]
    #[DataProvider('negativeNumberCases')]
    public function 負数の処理(float $input, RoundingMode $mode, int $level, int|float $expected): void
    {
        self::assertSame($expected, Rounding::apply($input, $mode, $level));
    }

    #[Test]
    public function ゼロの処理(): void
    {
        self::assertSame(0, Rounding::apply(0, RoundingMode::Round, 0));
        self::assertSame(0.0, Rounding::apply(0.0, RoundingMode::Round, 2));
    }
}
