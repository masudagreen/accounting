<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Dto;

use App\Application\Dto\BalanceSheetDto;
use PHPUnit\Framework\TestCase;

/**
 * BalanceSheetDto のユニットテスト.
 */
final class BalanceSheetDtoTest extends TestCase
{
    public function testToArrayContainsAllBalanceSheetFields(): void
    {
        // Arrange
        $dto = new BalanceSheetDto(
            totalAssets: 5000000,
            totalLiabilities: 2000000,
            totalEquity: 3000000,
        );

        // Act
        $arr = $dto->toArray();

        // Assert
        $this->assertSame(5000000, $arr['totalAssets']);
        $this->assertSame(2000000, $arr['totalLiabilities']);
        $this->assertSame(3000000, $arr['totalEquity']);
    }

    public function testZeroBalanceSheetIsValid(): void
    {
        $dto = new BalanceSheetDto(0, 0, 0);
        $arr = $dto->toArray();
        $this->assertSame(0, $arr['totalAssets']);
    }
}
