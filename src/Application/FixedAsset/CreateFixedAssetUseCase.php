<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetCode;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Register a new fixed asset in the ledger.
 *
 * Enforces:
 *   - asset code unique per entity (returns VALIDATION_FAILED when already taken).
 *   - method string resolves to a known {@see DepreciationMethod}.
 *   - all date / decimal invariants via the aggregate constructor.
 */
final readonly class CreateFixedAssetUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateFixedAssetInput $input): CreateFixedAssetOutput
    {
        // Validate code format eagerly so a bad code surfaces here.
        new FixedAssetCode($input->assetCode);

        $existing = $this->assets->findByEntityAndCode($input->entityId, $input->assetCode);
        if ($existing !== null) {
            throw ValidationException::withErrors([
                'assetCode' => [sprintf('assetCode "%s" is already in use for this entity.', $input->assetCode)],
            ]);
        }

        $method = DepreciationMethod::tryFrom($input->method);
        if ($method === null) {
            throw ValidationException::withErrors([
                'method' => [sprintf('method "%s" is not a supported depreciation method.', $input->method)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $asset = new FixedAsset(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            assetCode: $input->assetCode,
            assetName: $input->assetName,
            categoryCode: $input->categoryCode,
            assetAccountTitleId: $input->assetAccountTitleId,
            accumulatedDepreciationAccountTitleId: $input->accumulatedDepreciationAccountTitleId,
            depreciationExpenseAccountTitleId: $input->depreciationExpenseAccountTitleId,
            acquisitionDate: $input->acquisitionDate,
            serviceStartDate: $input->serviceStartDate,
            disposalDate: null,
            acquisitionCost: $input->acquisitionCost,
            residualValue: $input->residualValue,
            usefulLifeYears: $input->usefulLifeYears,
            method: $method,
            quantity: $input->quantity,
            departmentCode: $input->departmentCode,
            note: $input->note,
            createdBy: $input->createdBy,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );

        $this->assets->save($asset);

        return new CreateFixedAssetOutput($asset);
    }
}
