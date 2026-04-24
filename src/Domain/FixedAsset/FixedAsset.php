<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Fixed asset aggregate root (固定資産).
 *
 * Mirrors the legacy `accountingLogFixedAssetsJpn` row but carries only the
 * minimum set of fields needed to drive depreciation and posting.
 *
 * Invariants enforced at construction:
 *   - acquisition_date <= service_start_date
 *   - disposal_date (when set) >= service_start_date
 *   - acquisition_cost > 0
 *   - residual_value >= 0, residual_value <= acquisition_cost
 *   - useful_life_years >= 0 (allowed to be 0 for method="none"/"one_shot")
 */
final readonly class FixedAsset
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $assetCode,
        public string $assetName,
        public string $categoryCode,
        public ?string $assetAccountTitleId,
        public ?string $accumulatedDepreciationAccountTitleId,
        public ?string $depreciationExpenseAccountTitleId,
        public DateTimeImmutable $acquisitionDate,
        public DateTimeImmutable $serviceStartDate,
        public ?DateTimeImmutable $disposalDate,
        public string $acquisitionCost,
        public string $residualValue,
        public int $usefulLifeYears,
        public DepreciationMethod $method,
        public int $quantity,
        public ?string $departmentCode,
        public ?string $note,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt,
    ) {
        if ($acquisitionDate > $serviceStartDate) {
            throw ValidationException::withErrors([
                'serviceStartDate' => ['serviceStartDate must be on or after acquisitionDate.'],
            ]);
        }
        if ($disposalDate !== null && $disposalDate < $serviceStartDate) {
            throw ValidationException::withErrors([
                'disposalDate' => ['disposalDate must be on or after serviceStartDate.'],
            ]);
        }
        if (Decimal::compare($acquisitionCost, '0.0000') <= 0) {
            throw ValidationException::withErrors([
                'acquisitionCost' => ['acquisitionCost must be > 0.'],
            ]);
        }
        if (Decimal::compare($residualValue, '0.0000') < 0) {
            throw ValidationException::withErrors([
                'residualValue' => ['residualValue must be >= 0.'],
            ]);
        }
        if (Decimal::compare($residualValue, $acquisitionCost) > 0) {
            throw ValidationException::withErrors([
                'residualValue' => ['residualValue must be <= acquisitionCost.'],
            ]);
        }
        if ($usefulLifeYears < 0) {
            throw ValidationException::withErrors([
                'usefulLifeYears' => ['usefulLifeYears must be >= 0.'],
            ]);
        }
        if ($quantity < 1) {
            throw ValidationException::withErrors([
                'quantity' => ['quantity must be >= 1.'],
            ]);
        }
    }

    public function isDepreciable(): bool
    {
        return $this->method->isDepreciable() && $this->deletedAt === null;
    }

    public function dispose(DateTimeImmutable $at): self
    {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            assetCode: $this->assetCode,
            assetName: $this->assetName,
            categoryCode: $this->categoryCode,
            assetAccountTitleId: $this->assetAccountTitleId,
            accumulatedDepreciationAccountTitleId: $this->accumulatedDepreciationAccountTitleId,
            depreciationExpenseAccountTitleId: $this->depreciationExpenseAccountTitleId,
            acquisitionDate: $this->acquisitionDate,
            serviceStartDate: $this->serviceStartDate,
            disposalDate: $at,
            acquisitionCost: $this->acquisitionCost,
            residualValue: $this->residualValue,
            usefulLifeYears: $this->usefulLifeYears,
            method: $this->method,
            quantity: $this->quantity,
            departmentCode: $this->departmentCode,
            note: $this->note,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $at,
            deletedAt: $this->deletedAt,
        );
    }
}
