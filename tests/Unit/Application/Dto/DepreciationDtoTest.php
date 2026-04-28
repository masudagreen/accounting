<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Dto;

use App\Application\Dto\DepreciationDto;
use PHPUnit\Framework\TestCase;

/**
 * DepreciationDto のユニットテスト.
 */
final class DepreciationDtoTest extends TestCase
{
    public function testToArrayContainsAllDepreciationFields(): void
    {
        // Arrange
        $dto = new DepreciationDto(
            assetId: 'asset-001',
            assetName: '営業車両',
            depreciation: 200000,
            accumulatedClosing: 400000,
            bookValueClosing: 600000,
            monthsUsedInPeriod: 12,
        );

        // Act
        $arr = $dto->toArray();

        // Assert
        $this->assertSame('asset-001', $arr['assetId']);
        $this->assertSame('営業車両', $arr['assetName']);
        $this->assertSame(200000, $arr['depreciation']);
        $this->assertSame(400000, $arr['accumulatedClosing']);
        $this->assertSame(600000, $arr['bookValueClosing']);
        $this->assertSame(12, $arr['monthsUsedInPeriod']);
    }

    public function testZeroDepreciationIsValid(): void
    {
        $dto = new DepreciationDto('x', 'Y', 0, 0, 1000000, 12);
        $arr = $dto->toArray();
        $this->assertSame(0, $arr['depreciation']);
    }
}
