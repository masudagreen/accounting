<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;

/**
 * A single taxable period (課税期間).
 *
 * Ports the 課税期間 concept from the legacy plugin's
 * 消費税申告書 preference screens. Carries the calculation method and,
 * for simplified taxation, the chosen 事業区分.
 *
 * Invariants:
 *   - periodFrom <= periodTo;
 *   - simplifiedBusinessCategory is non-null iff method = Simplified.
 */
final readonly class ConsumptionTaxPeriod
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $periodFrom,
        public DateTimeImmutable $periodTo,
        public ConsumptionTaxCalculationMethod $calculationMethod,
        public ?SimplifiedBusinessCategory $simplifiedBusinessCategory,
        public bool $isInterim,
        public string $settlementStatus,
        public ?DateTimeImmutable $settledAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
        if ($periodTo < $periodFrom) {
            throw ValidationException::withErrors([
                'periodTo' => ['periodTo must be on or after periodFrom.'],
            ]);
        }
        if ($calculationMethod === ConsumptionTaxCalculationMethod::Simplified && $simplifiedBusinessCategory === null) {
            throw ValidationException::withErrors([
                'simplifiedBusinessCategory' => ['simplifiedBusinessCategory is required when method = simplified.'],
            ]);
        }
        if ($calculationMethod !== ConsumptionTaxCalculationMethod::Simplified && $simplifiedBusinessCategory !== null) {
            throw ValidationException::withErrors([
                'simplifiedBusinessCategory' => ['simplifiedBusinessCategory must be null unless method = simplified.'],
            ]);
        }
        if (!in_array($settlementStatus, ['pending', 'calculated', 'filed', 'paid'], true)) {
            throw ValidationException::withErrors([
                'settlementStatus' => ['settlementStatus must be pending/calculated/filed/paid.'],
            ]);
        }
    }

    public function contains(DateTimeImmutable $at): bool
    {
        return $at >= $this->periodFrom && $at <= $this->periodTo;
    }

    public function withStatus(string $status, ?DateTimeImmutable $settledAt, DateTimeImmutable $now): self
    {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            periodFrom: $this->periodFrom,
            periodTo: $this->periodTo,
            calculationMethod: $this->calculationMethod,
            simplifiedBusinessCategory: $this->simplifiedBusinessCategory,
            isInterim: $this->isInterim,
            settlementStatus: $status,
            settledAt: $settledAt,
            createdAt: $this->createdAt,
            updatedAt: $now,
        );
    }
}
