<?php

declare(strict_types=1);

namespace App\Domain\Money;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;

/**
 * 円建て金額の値オブジェクト。
 * 内部表現は任意精度十進 (Brick\Math\BigDecimal)。float の誤差を排除する。
 *
 * 不変。すべての演算は新しい Money を返す。
 */
final readonly class Money
{
    private function __construct(
        private BigDecimal $amount,
    ) {
    }

    public static function ofYen(int|string|BigDecimal $value): self
    {
        if ($value instanceof BigDecimal) {
            return new self($value);
        }
        return new self(BigDecimal::of((string) $value));
    }

    public static function zero(): self
    {
        return new self(BigDecimal::zero());
    }

    public function isZero(): bool
    {
        return $this->amount->isZero();
    }

    public function isNegative(): bool
    {
        return $this->amount->isNegative();
    }

    public function isPositive(): bool
    {
        return $this->amount->isPositive();
    }

    public function plus(Money $other): self
    {
        return new self($this->amount->plus($other->amount));
    }

    public function minus(Money $other): self
    {
        return new self($this->amount->minus($other->amount));
    }

    public function negate(): self
    {
        return new self($this->amount->negated());
    }

    public function multipliedBy(int|string|BigDecimal $multiplier): self
    {
        $multiplierBd = $multiplier instanceof BigDecimal
            ? $multiplier
            : BigDecimal::of((string) $multiplier);
        return new self($this->amount->multipliedBy($multiplierBd));
    }

    public function equals(Money $other): bool
    {
        return $this->amount->isEqualTo($other->amount);
    }

    public function isLessThan(Money $other): bool
    {
        return $this->amount->isLessThan($other->amount);
    }

    public function isLessThanOrEqualTo(Money $other): bool
    {
        return $this->amount->isLessThanOrEqualTo($other->amount);
    }

    public function isGreaterThan(Money $other): bool
    {
        return $this->amount->isGreaterThan($other->amount);
    }

    public function isGreaterThanOrEqualTo(Money $other): bool
    {
        return $this->amount->isGreaterThanOrEqualTo($other->amount);
    }

    /**
     * 円単位 (小数桁0) で端数処理する。
     */
    public function roundedToYen(RoundingMode $mode): self
    {
        $brickMode = match ($mode) {
            RoundingMode::Floor => BrickRoundingMode::FLOOR,
            RoundingMode::Ceil  => BrickRoundingMode::CEILING,
            RoundingMode::Round => BrickRoundingMode::HALF_UP,
        };
        return new self($this->amount->toScale(0, $brickMode));
    }

    /**
     * @internal テスト/ログ向け。本番ロジックでは equals 等を使うこと。
     */
    public function toString(): string
    {
        return (string) $this->amount;
    }

    public function toBigDecimal(): BigDecimal
    {
        return $this->amount;
    }
}
