<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Patch a subset of fields on an existing fixed asset. Fields left null in
 * the input keep their existing value. Validation rules run via the aggregate
 * constructor.
 */
final readonly class UpdateFixedAssetUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @param array{
     *     assetName?: string,
     *     categoryCode?: string,
     *     assetAccountTitleId?: ?string,
     *     accumulatedDepreciationAccountTitleId?: ?string,
     *     depreciationExpenseAccountTitleId?: ?string,
     *     residualValue?: string,
     *     usefulLifeYears?: int,
     *     method?: string,
     *     quantity?: int,
     *     departmentCode?: ?string,
     *     note?: ?string,
     * } $patch
     */
    public function execute(string $id, array $patch): FixedAsset
    {
        $existing = $this->assets->findById($id);
        if ($existing === null) {
            throw EntityNotFoundException::for('fixed_asset', $id);
        }

        $method = $existing->method;
        if (isset($patch['method'])) {
            $maybe = DepreciationMethod::tryFrom($patch['method']);
            if ($maybe === null) {
                throw ValidationException::withErrors([
                    'method' => [sprintf('method "%s" is not a supported depreciation method.', $patch['method'])],
                ]);
            }
            $method = $maybe;
        }

        $now = $this->clock->getCurrentTime();
        $next = new FixedAsset(
            id: $existing->id,
            entityId: $existing->entityId,
            assetCode: $existing->assetCode,
            assetName: (string) ($patch['assetName'] ?? $existing->assetName),
            categoryCode: (string) ($patch['categoryCode'] ?? $existing->categoryCode),
            assetAccountTitleId: array_key_exists('assetAccountTitleId', $patch)
                ? $patch['assetAccountTitleId']
                : $existing->assetAccountTitleId,
            accumulatedDepreciationAccountTitleId: array_key_exists('accumulatedDepreciationAccountTitleId', $patch)
                ? $patch['accumulatedDepreciationAccountTitleId']
                : $existing->accumulatedDepreciationAccountTitleId,
            depreciationExpenseAccountTitleId: array_key_exists('depreciationExpenseAccountTitleId', $patch)
                ? $patch['depreciationExpenseAccountTitleId']
                : $existing->depreciationExpenseAccountTitleId,
            acquisitionDate: $existing->acquisitionDate,
            serviceStartDate: $existing->serviceStartDate,
            disposalDate: $existing->disposalDate,
            acquisitionCost: $existing->acquisitionCost,
            residualValue: (string) ($patch['residualValue'] ?? $existing->residualValue),
            usefulLifeYears: (int) ($patch['usefulLifeYears'] ?? $existing->usefulLifeYears),
            method: $method,
            quantity: (int) ($patch['quantity'] ?? $existing->quantity),
            departmentCode: array_key_exists('departmentCode', $patch) ? $patch['departmentCode'] : $existing->departmentCode,
            note: array_key_exists('note', $patch) ? $patch['note'] : $existing->note,
            createdBy: $existing->createdBy,
            createdAt: $existing->createdAt,
            updatedAt: $now,
            deletedAt: $existing->deletedAt,
        );
        $this->assets->save($next);
        return $next;
    }
}
