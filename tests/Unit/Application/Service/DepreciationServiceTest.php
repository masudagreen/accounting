<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\DepreciationService;
use App\Domain\Depreciation\Acquisition;
use App\Domain\FixedAssets\DepreciationMethodChoice;
use App\Domain\FixedAssets\FixedAsset;
use App\Domain\FixedAssets\FixedAssetAccountMapping;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use App\Infrastructure\Persistence\FixedAssetRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * DepreciationService のユニットテスト.
 */
final class DepreciationServiceTest extends TestCase
{
    private FixedAssetRepository&MockObject $repo;
    private DepreciationService $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(FixedAssetRepository::class);
        $this->service = new DepreciationService($this->repo);
    }

    private function makeStraightLineAsset(): FixedAsset
    {
        $acquisition = new Acquisition(
            cost: Money::ofYen(1000000),
            usefulLifeYears: 5,
            acquisitionDate: new DateTimeImmutable('2024-04-01'),
            businessUseRatioPercent: 100,
        );
        $method  = DepreciationMethodChoice::Straight;
        $mapping = new FixedAssetAccountMapping('depExpense', 'accumDep');

        return new FixedAsset('asset-1', 'テスト機械', $acquisition, $method, $mapping);
    }

    public function testComputeForAssetReturnsDtoWithDepreciationAmount(): void
    {
        // Arrange
        $asset  = $this->makeStraightLineAsset();
        $period = FiscalPeriod::of(2024, 4, 12, 1);

        $this->repo->method('findById')->with('asset-1')->willReturn($asset);

        // Act
        $dto = $this->service->computeForAsset(
            assetId: 'asset-1',
            period: $period,
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        // Assert
// assertIsArray removed (already typed as array)
        $this->assertArrayHasKey('assetId', $dto);
        $this->assertArrayHasKey('assetName', $dto);
        $this->assertArrayHasKey('depreciation', $dto);
        $this->assertArrayHasKey('accumulatedClosing', $dto);
        $this->assertArrayHasKey('bookValueClosing', $dto);
        $this->assertSame('asset-1', $dto['assetId']);
        $this->assertSame('テスト機械', $dto['assetName']);
        // 定額法: 1,000,000 / 5 = 200,000
        $this->assertSame(200000, $dto['depreciation']);
    }

    public function testComputeForAssetThrowsWhenAssetNotFound(): void
    {
        // Arrange
        $this->repo->method('findById')->willReturn(null);

        $period = FiscalPeriod::of(2024, 4, 12, 1);

        // Assert + Act
        $this->expectException(\RuntimeException::class);
        $this->service->computeForAsset(
            assetId: 'nonexistent',
            period: $period,
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );
    }

    public function testComputeForAllAssetsReturnsListOfDtos(): void
    {
        // Arrange
        $asset  = $this->makeStraightLineAsset();
        $period = FiscalPeriod::of(2024, 4, 12, 1);

        $this->repo
            ->method('findByEntity')
            ->with(99)
            ->willReturn([$asset]);

        // Act
        $dtos = $this->service->computeForAllAssets(
            idEntity: 99,
            period: $period,
            previousAccumulatedMap: [],
            mode: RoundingMode::Floor,
        );

        // Assert
        $this->assertCount(1, $dtos);
        $this->assertSame('asset-1', $dtos[0]['assetId']);
    }

    public function testComputeForAllAssetsReturnsEmptyArrayWhenNoAssets(): void
    {
        // Arrange
        $period = FiscalPeriod::of(2024, 4, 12, 1);
        $this->repo->method('findByEntity')->willReturn([]);

        // Act
        $dtos = $this->service->computeForAllAssets(
            idEntity: 1,
            period: $period,
            previousAccumulatedMap: [],
            mode: RoundingMode::Floor,
        );

        // Assert
        $this->assertSame([], $dtos);
    }
}
