<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\DepreciationDto;
use App\Domain\FixedAssets\FixedAsset;
use App\Domain\FixedAssets\FixedAssetJournalGenerator;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use App\Infrastructure\Persistence\FixedAssetRepository;

/**
 * 固定資産の減価償却計算サービス.
 *
 * FixedAssetJournalGenerator に計算を委譲し、DepreciationDto に変換する.
 */
final class DepreciationService
{
    public function __construct(
        private readonly FixedAssetRepository $repository,
    ) {
    }

    /**
     * 指定資産の1期分の減価償却を計算して DTO 配列を返す.
     *
     * @return array<string, mixed>
     * @throws \RuntimeException 資産が見つからない場合
     */
    public function computeForAsset(
        string $assetId,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $mode,
        ?Money $voluntaryAmount = null,
        int $yearIndex = 1,
    ): array {
        $asset = $this->repository->findById($assetId);
        if ($asset === null) {
            throw new \RuntimeException(sprintf('FixedAsset not found: %s', $assetId));
        }

        return $this->computeForAssetObject(
            asset: $asset,
            period: $period,
            previousAccumulated: $previousAccumulated,
            mode: $mode,
            voluntaryAmount: $voluntaryAmount,
            yearIndex: $yearIndex,
        );
    }

    /**
     * 指定事業体の全資産の減価償却を計算してリストで返す.
     *
     * @param array<string, Money> $previousAccumulatedMap assetId => 前期末累計額
     * @return list<array<string, mixed>>
     */
    public function computeForAllAssets(
        int $idEntity,
        FiscalPeriod $period,
        array $previousAccumulatedMap,
        RoundingMode $mode,
    ): array {
        $assets = $this->repository->findByEntity($idEntity);

        $result = [];
        foreach ($assets as $asset) {
            $previousAccumulated = $previousAccumulatedMap[$asset->id()] ?? Money::zero();
            $result[] = $this->computeForAssetObject(
                asset: $asset,
                period: $period,
                previousAccumulated: $previousAccumulated,
                mode: $mode,
            );
        }

        return $result;
    }

    /**
     * FixedAsset オブジェクトを直接受け取って計算する内部メソッド.
     *
     * @return array<string, mixed>
     */
    private function computeForAssetObject(
        FixedAsset $asset,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $mode,
        ?Money $voluntaryAmount = null,
        int $yearIndex = 1,
    ): array {
        $journalEntry = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $period,
            previousAccumulated: $previousAccumulated,
            mode: $mode,
            voluntaryAmount: $voluntaryAmount,
            yearIndex: $yearIndex,
        );

        $depreciationAmount = $journalEntry !== null
            ? (int) $journalEntry->totalDebits()->toString()
            : 0;

        $accumulatedClosing = (int) $previousAccumulated->plus(Money::ofYen($depreciationAmount))->toString();
        $bookValueClosing   = (int) $asset->acquisition()->cost()->minus(Money::ofYen($accumulatedClosing))->toString();
        $monthsUsedInPeriod = $period->termMonths();

        $dto = new DepreciationDto(
            assetId: $asset->id(),
            assetName: $asset->name(),
            depreciation: $depreciationAmount,
            accumulatedClosing: $accumulatedClosing,
            bookValueClosing: $bookValueClosing,
            monthsUsedInPeriod: $monthsUsedInPeriod,
        );

        return $dto->toArray();
    }
}
