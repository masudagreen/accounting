<?php

declare(strict_types=1);

namespace Rucaro\Domain\CashPlan;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Aggregate root for the 資金繰り表 (cash plan).
 *
 * Carries the header (entity + fiscal term + opening balance + currency)
 * plus a list of {@see CashPlanEntry} lines. Running monthly / closing
 * balances are always derived, not stored, so the view layer never has to
 * trust hand-edited totals.
 *
 * Invariants:
 *   - `name` is non-empty and <= 128 chars;
 *   - `openingBalance` is a scale-4 decimal string (negative allowed: a
 *     cash plan can legitimately start from an overdraft);
 *   - `currencyCode` is exactly 3 uppercase ASCII letters.
 */
final readonly class CashPlan
{
    /**
     * @param list<CashPlanEntry> $entries
     */
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public string $name,
        public string $openingBalance,
        public string $currencyCode,
        public ?string $notes,
        public array $entries,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt = null,
    ) {
        if ($name === '' || mb_strlen($name) > 128) {
            throw ValidationException::withErrors([
                'name' => ['name must be 1..128 characters.'],
            ]);
        }
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw ValidationException::withErrors([
                'currencyCode' => ['currencyCode must be 3 uppercase ASCII letters.'],
            ]);
        }
        // openingBalance validation happens via Decimal::normalize below.
        Decimal::normalize($openingBalance);
    }

    /**
     * Sum inflows/outflows for a given fiscal month (1..12). Returns the
     * signed delta (positive = net inflow).
     */
    public function monthlyDelta(int $month): string
    {
        $delta = '0.0000';
        foreach ($this->entries as $e) {
            $amount = $e->amountForMonth($month);
            if ($e->category->isInflow()) {
                $delta = Decimal::add($delta, $amount);
            } else {
                $delta = Decimal::add($delta, '-' . ltrim(Decimal::normalize($amount), '-'));
            }
        }
        return Decimal::normalize($delta);
    }

    /**
     * Running balance at the end of fiscal month `$month` (1..12).
     * balance(n) = openingBalance + sum(delta(1..n))
     */
    public function closingBalance(int $month): string
    {
        if ($month < 1 || $month > CashPlanEntry::MONTHS) {
            throw ValidationException::withErrors([
                'month' => [sprintf('month must be in 1..%d.', CashPlanEntry::MONTHS)],
            ]);
        }
        $balance = Decimal::normalize($this->openingBalance);
        for ($m = 1; $m <= $month; $m++) {
            $balance = Decimal::add($balance, $this->monthlyDelta($m));
        }
        return Decimal::normalize($balance);
    }

    /**
     * @return array{operating_in:string, operating_out:string, investing_in:string, investing_out:string, financing_in:string, financing_out:string}
     */
    public function totalsByCategory(): array
    {
        /** @var array<string, string> $sums */
        $sums = [
            'operating_in'  => '0.0000',
            'operating_out' => '0.0000',
            'investing_in'  => '0.0000',
            'investing_out' => '0.0000',
            'financing_in'  => '0.0000',
            'financing_out' => '0.0000',
        ];
        foreach ($this->entries as $e) {
            $key = $e->category->value;
            $sums[$key] = Decimal::add($sums[$key], $e->total());
        }
        return [
            'operating_in'  => Decimal::normalize($sums['operating_in']),
            'operating_out' => Decimal::normalize($sums['operating_out']),
            'investing_in'  => Decimal::normalize($sums['investing_in']),
            'investing_out' => Decimal::normalize($sums['investing_out']),
            'financing_in'  => Decimal::normalize($sums['financing_in']),
            'financing_out' => Decimal::normalize($sums['financing_out']),
        ];
    }

    /**
     * Replace the entries list, returning a new {@see CashPlan}.
     *
     * @param list<CashPlanEntry> $entries
     */
    public function withEntries(array $entries, DateTimeImmutable $now): self
    {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $this->name,
            openingBalance: $this->openingBalance,
            currencyCode: $this->currencyCode,
            notes: $this->notes,
            entries: $entries,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    public function withHeader(
        string $name,
        string $openingBalance,
        string $currencyCode,
        ?string $notes,
        DateTimeImmutable $now,
    ): self {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $name,
            openingBalance: $openingBalance,
            currencyCode: $currencyCode,
            notes: $notes,
            entries: $this->entries,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }
}
