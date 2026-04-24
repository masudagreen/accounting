<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\BlueReturn;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\BlueReturn\GenerateBlueReturnSnapshotInput;
use Rucaro\Application\BlueReturn\GenerateBlueReturnSnapshotUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;

#[CoversClass(GenerateBlueReturnSnapshotUseCase::class)]
final class GenerateBlueReturnSnapshotUseCaseTest extends TestCase
{
    public function testProducesSnapshotFromBucketedInput(): void
    {
        $uc = new GenerateBlueReturnSnapshotUseCase();
        $snap = $uc->execute(new GenerateBlueReturnSnapshotInput(
            formType: BlueReturnFormType::General,
            revenueByAccount: ['売上' => '1000000'],
            costOfSalesByAccount: ['仕入' => '400000'],
            expensesByAccount: ['給料賃金' => '200000'],
            monthlyRows: [],
            breakdown: [],
            assetsByAccount: ['現金' => '600000'],
            liabilitiesByAccount: [],
            equityByAccount: ['元入金' => '600000'],
        ));

        self::assertSame('400000.0000', $snap->page1Pl['netIncome']);
        self::assertSame('600000.0000', $snap->page4Bs['assetsTotal']);
    }
}
