<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Dto;

use App\Application\Dto\TrialBalanceDto;
use PHPUnit\Framework\TestCase;

/**
 * TrialBalanceDto のユニットテスト.
 */
final class TrialBalanceDtoTest extends TestCase
{
    public function testToArrayReturnsExpectedKeys(): void
    {
        // Arrange
        $dto = new TrialBalanceDto(
            id: 'cash',
            title: '現金',
            opening: 100000,
            periodDebits: 50000,
            periodCredits: 30000,
            closing: 120000,
        );

        // Act
        $arr = $dto->toArray();

        // Assert
        $this->assertSame('cash', $arr['id']);
        $this->assertSame('現金', $arr['title']);
        $this->assertSame(100000, $arr['opening']);
        $this->assertSame(50000, $arr['periodDebits']);
        $this->assertSame(30000, $arr['periodCredits']);
        $this->assertSame(120000, $arr['closing']);
    }

    public function testZeroValuesAreAllowed(): void
    {
        // Arrange + Act
        $dto = new TrialBalanceDto(
            id: 'someAccount',
            title: '何かの科目',
            opening: 0,
            periodDebits: 0,
            periodCredits: 0,
            closing: 0,
        );

        // Assert
        $this->assertSame(0, $dto->toArray()['closing']);
    }

    public function testToArrayIsIdempotent(): void
    {
        // Arrange
        $dto = new TrialBalanceDto('x', 'X', 1, 2, 3, 0);

        // Act + Assert
        $this->assertSame($dto->toArray(), $dto->toArray());
    }
}
