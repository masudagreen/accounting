<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Validation\AbstractValueObject;

/**
 * Calendar date (UTC, no time-of-day) on which a {@see \Rucaro\Domain\Journal\Journal}
 * was booked.
 *
 * Internal storage is a {@see DateTimeImmutable} pinned to UTC so value-level
 * equality is unambiguous across timezones. The primitive representation is
 * the ISO 8601 date string `YYYY-MM-DD`, which matches ADR-002's
 * `journal_entries.journal_date DATE` column and the `/api/v1/journals`
 * OpenAPI contract.
 */
final readonly class JournalDate extends AbstractValueObject
{
    private DateTimeImmutable $value;

    public function __construct(DateTimeInterface $value)
    {
        $this->value = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $value->format('Y-m-d'),
            new DateTimeZone('UTC'),
        ) ?: new DateTimeImmutable('@0');
    }

    public static function fromString(string $raw): self
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw) !== 1) {
            throw ValidationException::withErrors([
                'journalDate' => ['journalDate must be an ISO 8601 date (YYYY-MM-DD).'],
            ]);
        }
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $raw, new DateTimeZone('UTC'));
        if ($d === false || $d->format('Y-m-d') !== $raw) {
            throw ValidationException::withErrors([
                'journalDate' => ['journalDate must be a real calendar date.'],
            ]);
        }
        return new self($d);
    }

    public function toDateTime(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toPrimitive(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isOnOrBefore(self $other): bool
    {
        return $this->value <= $other->value;
    }

    public function isOnOrAfter(self $other): bool
    {
        return $this->value >= $other->value;
    }
}
