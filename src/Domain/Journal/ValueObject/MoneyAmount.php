<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\ValueObject;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;
use Rucaro\Support\Validation\AbstractValueObject;

/**
 * Non-negative money amount stored at DECIMAL(18,4) precision.
 *
 * Journals only ever carry *magnitudes*; the `side` field on each line
 * decides whether the value increases the debit or credit column, so storing
 * negatives here would admit ambiguous "negative debit" states. Zero is
 * allowed to keep reversals and tax-only lines representable.
 */
final readonly class MoneyAmount extends AbstractValueObject
{
    public string $value;

    public function __construct(string $value)
    {
        if (preg_match('/^-?\d{1,14}(\.\d{1,4})?$/', $value) !== 1) {
            throw ValidationException::withErrors([
                'amount' => ['amount must match DECIMAL(18,4) format.'],
            ]);
        }
        $normalized = Decimal::normalize($value);
        if (Decimal::compare($normalized, '0.0000') < 0) {
            throw ValidationException::withErrors([
                'amount' => ['amount must be >= 0.'],
            ]);
        }
        $this->value = $normalized;
    }

    public static function zero(): self
    {
        return new self('0.0000');
    }

    public static function fromString(string $raw): self
    {
        return new self($raw);
    }

    public function plus(self $other): self
    {
        return new self(Decimal::add($this->value, $other->value));
    }

    /**
     * Subtracts `$other` from `$this`. Guarded so the result cannot dip
     * below zero — callers should check {@see self::isGreaterThanOrEqual}
     * before subtracting.
     */
    public function minus(self $other): self
    {
        // Subtract by adding the negated operand through the Decimal helper.
        $negated = str_starts_with($other->value, '-')
            ? substr($other->value, 1)
            : '-' . $other->value;
        $result = Decimal::add($this->value, $negated);
        if (Decimal::compare($result, '0.0000') < 0) {
            throw ValidationException::withErrors([
                'amount' => ['subtraction would yield a negative amount.'],
            ]);
        }
        return new self($result);
    }

    public function isZero(): bool
    {
        return Decimal::compare($this->value, '0.0000') === 0;
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        return Decimal::compare($this->value, $other->value) >= 0;
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
