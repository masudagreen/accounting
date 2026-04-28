<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FixedAssets;

use App\Domain\Depreciation\Acquisition;
use App\Domain\FixedAssets\DepreciationMethodChoice;
use App\Domain\FixedAssets\FixedAsset;
use App\Domain\FixedAssets\FixedAssetAccountMapping;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FixedAsset::class)]
#[CoversClass(DepreciationMethodChoice::class)]
#[CoversClass(FixedAssetAccountMapping::class)]
final class FixedAssetTest extends TestCase
{
    private function makeAcquisition(int $costYen = 1_200_000, int $years = 5): Acquisition
    {
        return new Acquisition(
            cost: Money::ofYen($costYen),
            usefulLifeYears: $years,
            acquisitionDate: new \DateTimeImmutable('2026-04-01'),
        );
    }

    private function makeMapping(): FixedAssetAccountMapping
    {
        return new FixedAssetAccountMapping(
            depreciationExpenseAccountTitleId: 'depreciation-expense',
            accumulatedDepreciationAccountTitleId: 'accumulated-depreciation',
        );
    }

    #[Test]
    public function 固定資産が正常に生成できる(): void
    {
        $asset = new FixedAsset(
            id: 'fa-001',
            name: '機械装置A',
            acquisition: $this->makeAcquisition(),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );

        self::assertSame('fa-001', $asset->id());
        self::assertSame('機械装置A', $asset->name());
        self::assertSame(DepreciationMethodChoice::Straight, $asset->method());
        self::assertSame('depreciation-expense', $asset->accountMapping()->depreciationExpenseAccountTitleId());
        self::assertSame('accumulated-depreciation', $asset->accountMapping()->accumulatedDepreciationAccountTitleId());
    }

    #[Test]
    public function idが空文字の場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('id must not be empty');

        new FixedAsset(
            id: '',
            name: '機械装置A',
            acquisition: $this->makeAcquisition(),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );
    }

    #[Test]
    public function nameが空文字の場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('name must not be empty');

        new FixedAsset(
            id: 'fa-002',
            name: '',
            acquisition: $this->makeAcquisition(),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );
    }

    #[Test]
    public function DepreciationMethodChoiceのenumケースを確認できる(): void
    {
        self::assertSame('straight', DepreciationMethodChoice::Straight->value);
        self::assertSame('declining200', DepreciationMethodChoice::Declining200->value);
        self::assertSame('declining250', DepreciationMethodChoice::Declining250->value);
        self::assertSame('sum_of_years', DepreciationMethodChoice::SumOfYears->value);
        self::assertSame('average', DepreciationMethodChoice::Average->value);
        self::assertSame('voluntary', DepreciationMethodChoice::Voluntary->value);
        self::assertSame('lump_sum_three_year', DepreciationMethodChoice::LumpSumThreeYear->value);
    }

    #[Test]
    public function FixedAssetAccountMappingが正常に生成できる(): void
    {
        $mapping = new FixedAssetAccountMapping(
            depreciationExpenseAccountTitleId: 'dep-exp',
            accumulatedDepreciationAccountTitleId: 'acc-dep',
        );

        self::assertSame('dep-exp', $mapping->depreciationExpenseAccountTitleId());
        self::assertSame('acc-dep', $mapping->accumulatedDepreciationAccountTitleId());
    }
}
