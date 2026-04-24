<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FixedAsset\DepreciationMethod;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetCode;

#[CoversClass(FixedAsset::class)]
#[CoversClass(FixedAssetCode::class)]
#[CoversClass(DepreciationMethod::class)]
final class FixedAssetTest extends TestCase
{
    public function testRejectsServiceStartBeforeAcquisition(): void
    {
        $this->expectException(ValidationException::class);
        new FixedAsset(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            assetCode: 'M-001',
            assetName: 'Machine',
            categoryCode: 'machinery',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: new DateTimeImmutable('2024-04-01'),
            serviceStartDate: new DateTimeImmutable('2024-03-01'),
            disposalDate: null,
            acquisitionCost: '1000000.0000',
            residualValue: '0.0000',
            usefulLifeYears: 10,
            method: DepreciationMethod::StraightLine,
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
            deletedAt: null,
        );
    }

    public function testRejectsNegativeAcquisitionCost(): void
    {
        $this->expectException(ValidationException::class);
        new FixedAsset(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            assetCode: 'M-001',
            assetName: 'Machine',
            categoryCode: 'machinery',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: new DateTimeImmutable('2024-04-01'),
            serviceStartDate: new DateTimeImmutable('2024-04-01'),
            disposalDate: null,
            acquisitionCost: '0.0000',
            residualValue: '0.0000',
            usefulLifeYears: 10,
            method: DepreciationMethod::StraightLine,
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
            deletedAt: null,
        );
    }

    public function testCodeRejectsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FixedAssetCode('');
    }

    public function testCodeRejectsInvalidCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FixedAssetCode('ABC 123');
    }

    public function testCodeAcceptsValidCharacters(): void
    {
        $c = new FixedAssetCode('M-001.v2_A');
        self::assertSame('M-001.v2_A', (string) $c);
    }

    public function testDepreciationMethodEnum(): void
    {
        self::assertTrue(DepreciationMethod::StraightLine->isDepreciable());
        self::assertFalse(DepreciationMethod::None->isDepreciable());
        self::assertTrue(DepreciationMethod::DecliningBalance2012->isDeclining());
        self::assertFalse(DepreciationMethod::StraightLine->isDeclining());
    }

    public function testDisposeMarksAssetWithDisposalDate(): void
    {
        $asset = new FixedAsset(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            assetCode: 'M-001',
            assetName: 'Machine',
            categoryCode: 'machinery',
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: new DateTimeImmutable('2024-04-01'),
            serviceStartDate: new DateTimeImmutable('2024-04-01'),
            disposalDate: null,
            acquisitionCost: '1000000.0000',
            residualValue: '0.0000',
            usefulLifeYears: 10,
            method: DepreciationMethod::StraightLine,
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            createdAt: new DateTimeImmutable('2024-04-01'),
            updatedAt: new DateTimeImmutable('2024-04-01'),
            deletedAt: null,
        );
        $disposed = $asset->dispose(new DateTimeImmutable('2026-03-31'));
        self::assertSame('2026-03-31', $disposed->disposalDate?->format('Y-m-d'));
    }
}
