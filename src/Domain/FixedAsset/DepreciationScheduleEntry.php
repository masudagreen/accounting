<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

use DateTimeImmutable;

/**
 * A single line (period) of a {@see DepreciationSchedule} — one fiscal
 * term's projected depreciation for one fixed asset.
 */
final readonly class DepreciationScheduleEntry
{
    public function __construct(
        public string $id,
        public string $fixedAssetId,
        public string $fiscalTermId,
        public int $periodNumber,
        public DateTimeImmutable $periodStartDate,
        public DateTimeImmutable $periodEndDate,
        public int $monthsInService,
        public string $openingBookValue,
        public string $depreciationAmount,
        public string $accumulatedDepreciation,
        public string $closingBookValue,
        public bool $isPosted,
        public ?string $postedJournalEntryId,
        public DateTimeImmutable $generatedAt,
    ) {
    }

    public function markPosted(string $journalEntryId, DateTimeImmutable $at): self
    {
        return new self(
            id: $this->id,
            fixedAssetId: $this->fixedAssetId,
            fiscalTermId: $this->fiscalTermId,
            periodNumber: $this->periodNumber,
            periodStartDate: $this->periodStartDate,
            periodEndDate: $this->periodEndDate,
            monthsInService: $this->monthsInService,
            openingBookValue: $this->openingBookValue,
            depreciationAmount: $this->depreciationAmount,
            accumulatedDepreciation: $this->accumulatedDepreciation,
            closingBookValue: $this->closingBookValue,
            isPosted: true,
            postedJournalEntryId: $journalEntryId,
            generatedAt: $at,
        );
    }
}
