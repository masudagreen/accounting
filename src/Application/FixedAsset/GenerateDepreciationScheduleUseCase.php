<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeImmutable;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculationRequest;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculatorFactory;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Decimal\Decimal;

/**
 * Generate (and upsert) the depreciation schedule row for one fiscal term
 * per eligible asset.
 *
 * Behaviour:
 *   - If `fixedAssetId` is supplied, only that asset is processed.
 *   - Otherwise every non-disposed asset owned by the entity is processed.
 *   - For each asset the use case looks up the existing schedule for the
 *     previous fiscal term (by period_number-1) to carry the opening book
 *     value forward; when none exists (first period) the opening book value
 *     equals the acquisition cost.
 *   - Months-in-service are computed from the asset's service_start_date and
 *     the fiscal term window: if service started before the term, 12 months
 *     apply; if started inside the term, only the remaining months count.
 *   - Existing schedule rows with `is_posted=1` are preserved (no overwrite).
 *     Unposted rows are replaced.
 */
final readonly class GenerateDepreciationScheduleUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
        private DepreciationScheduleRepositoryInterface $schedules,
        private DepreciationCalculatorFactory $calculatorFactory,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(GenerateDepreciationScheduleInput $input): GenerateDepreciationScheduleOutput
    {
        $targets = $input->fixedAssetId !== null
            ? array_filter(
                [$this->assets->findById($input->fixedAssetId)],
                static fn (?FixedAsset $a): bool => $a !== null,
            )
            : $this->assets->findByEntity($input->entityId, false);

        $results = [];
        foreach ($targets as $asset) {
            if (!$asset->isDepreciable()) {
                continue;
            }
            $existing = $this->schedules->findByAssetAndFiscalTerm(
                $asset->id,
                $input->fiscalTermId,
            );
            if ($existing !== null && $existing->isPosted) {
                $results[] = $existing;
                continue;
            }

            $periodNumber = $this->inferPeriodNumber($asset, $input->fiscalTermStart);
            $opening = $this->inferOpeningBookValue($asset, $periodNumber);
            $openingAccum = $this->inferOpeningAccumulatedDepreciation($asset, $periodNumber);
            $months = $this->inferMonthsInService(
                $asset,
                $input->fiscalTermStart,
                $input->fiscalTermEnd,
            );

            $calc = $this->calculatorFactory->resolve($asset->method);
            $result = $calc->calculate(new DepreciationCalculationRequest(
                acquisitionCost: $asset->acquisitionCost,
                residualValue: $asset->residualValue,
                usefulLifeYears: $asset->usefulLifeYears,
                serviceStartDate: $asset->serviceStartDate,
                periodStartDate: $input->fiscalTermStart,
                periodEndDate: $input->fiscalTermEnd,
                periodNumber: $periodNumber,
                monthsInService: $months,
                fiscalTermMonths: self::monthsBetween($input->fiscalTermStart, $input->fiscalTermEnd),
                openingBookValue: $opening,
                openingAccumulatedDepreciation: $openingAccum,
            ));

            $now = $this->clock->getCurrentTime();
            $entry = new DepreciationScheduleEntry(
                id: $existing?->id ?? $this->ulids->generate(),
                fixedAssetId: $asset->id,
                fiscalTermId: $input->fiscalTermId,
                periodNumber: $periodNumber,
                periodStartDate: $input->fiscalTermStart,
                periodEndDate: $input->fiscalTermEnd,
                monthsInService: $months,
                openingBookValue: $opening,
                depreciationAmount: $result->depreciationAmount,
                accumulatedDepreciation: $result->accumulatedDepreciation,
                closingBookValue: $result->closingBookValue,
                isPosted: false,
                postedJournalEntryId: null,
                generatedAt: $now,
            );
            $this->schedules->save($entry);
            $results[] = $entry;
        }
        return new GenerateDepreciationScheduleOutput($results);
    }

    private function inferPeriodNumber(FixedAsset $asset, DateTimeImmutable $fiscalTermStart): int
    {
        $prior = $this->schedules->findByAsset($asset->id);
        if ($prior === []) {
            return 1;
        }
        $max = 0;
        foreach ($prior as $e) {
            if ($e->periodNumber > $max) {
                $max = $e->periodNumber;
            }
        }
        return $max + 1;
    }

    private function inferOpeningBookValue(FixedAsset $asset, int $periodNumber): string
    {
        if ($periodNumber <= 1) {
            return Decimal::normalize($asset->acquisitionCost);
        }
        $prior = $this->schedules->findByAsset($asset->id);
        usort($prior, static fn ($a, $b) => $a->periodNumber <=> $b->periodNumber);
        $previous = null;
        foreach ($prior as $e) {
            if ($e->periodNumber === $periodNumber - 1) {
                $previous = $e;
                break;
            }
        }
        if ($previous === null) {
            return Decimal::normalize($asset->acquisitionCost);
        }
        return $previous->closingBookValue;
    }

    private function inferOpeningAccumulatedDepreciation(FixedAsset $asset, int $periodNumber): string
    {
        if ($periodNumber <= 1) {
            return '0.0000';
        }
        $prior = $this->schedules->findByAsset($asset->id);
        usort($prior, static fn ($a, $b) => $a->periodNumber <=> $b->periodNumber);
        foreach ($prior as $e) {
            if ($e->periodNumber === $periodNumber - 1) {
                return $e->accumulatedDepreciation;
            }
        }
        return '0.0000';
    }

    private function inferMonthsInService(
        FixedAsset $asset,
        DateTimeImmutable $termStart,
        DateTimeImmutable $termEnd,
    ): int {
        $effectiveStart = $asset->serviceStartDate > $termStart ? $asset->serviceStartDate : $termStart;
        if ($effectiveStart > $termEnd) {
            return 0;
        }
        $effectiveEnd = $asset->disposalDate !== null && $asset->disposalDate < $termEnd
            ? $asset->disposalDate
            : $termEnd;
        return max(0, self::monthsBetween($effectiveStart, $effectiveEnd));
    }

    private static function monthsBetween(DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        $diff = $start->diff($end);
        $months = $diff->y * 12 + $diff->m;
        if ($diff->d > 0) {
            $months += 1;
        }
        return max(1, $months);
    }
}
