<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FixedAssets;

use App\Domain\Depreciation\Acquisition;
use App\Domain\FixedAssets\DepreciationMethodChoice;
use App\Domain\FixedAssets\FixedAsset;
use App\Domain\FixedAssets\FixedAssetAccountMapping;
use App\Domain\FixedAssets\FixedAssetJournalGenerator;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FixedAssetJournalGenerator::class)]
final class FixedAssetJournalGeneratorTest extends TestCase
{
    private function makeMapping(): FixedAssetAccountMapping
    {
        return new FixedAssetAccountMapping(
            depreciationExpenseAccountTitleId: 'depreciation-expense',
            accumulatedDepreciationAccountTitleId: 'accumulated-depreciation',
        );
    }

    private function makePeriod(): FiscalPeriod
    {
        // 2026-04-01 〜 2027-03-31 (12ヶ月)
        return FiscalPeriod::of(2026, 4, 12, 1);
    }

    #[Test]
    public function 定額法で240000円の償却仕訳が生成できる(): void
    {
        // 取得価額1,200,000 耐用年数5年 → 年間240,000
        $asset = new FixedAsset(
            id: 'fa-001',
            name: '機械装置A',
            acquisition: new Acquisition(
                cost: Money::ofYen(1_200_000),
                usefulLifeYears: 5,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        self::assertNotNull($journal);
        self::assertCount(1, $journal->debits());
        self::assertCount(1, $journal->credits());

        // 借方: 減価償却費 240,000
        self::assertSame('depreciation-expense', $journal->debits()[0]->accountTitleId());
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(240_000)));

        // 貸方: 減価償却累計額 240,000
        self::assertSame('accumulated-depreciation', $journal->credits()[0]->accountTitleId());
        self::assertTrue($journal->credits()[0]->amount()->equals(Money::ofYen(240_000)));
    }

    #[Test]
    public function 定率法200パーセントで償却仕訳が生成できる(): void
    {
        // 取得価額1,000,000 耐用年数5年
        // 200%定率法 償却率0.400 → 400,000
        $asset = new FixedAsset(
            id: 'fa-002',
            name: '工具備品B',
            acquisition: new Acquisition(
                cost: Money::ofYen(1_000_000),
                usefulLifeYears: 5,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::Declining200,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        self::assertNotNull($journal);
        self::assertTrue($journal->isBalanced());
        // 借方勘定科目が減価償却費であること
        self::assertSame('depreciation-expense', $journal->debits()[0]->accountTitleId());
        // 貸方勘定科目が減価償却累計額であること
        self::assertSame('accumulated-depreciation', $journal->credits()[0]->accountTitleId());
        // 金額は400,000
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(400_000)));
    }

    #[Test]
    public function 任意償却でvoluntaryAmountを渡すと指定額の仕訳が生成できる(): void
    {
        $asset = new FixedAsset(
            id: 'fa-003',
            name: '建物C',
            acquisition: new Acquisition(
                cost: Money::ofYen(5_000_000),
                usefulLifeYears: 20,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::Voluntary,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
            voluntaryAmount: Money::ofYen(300_000),
        );

        self::assertNotNull($journal);
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(300_000)));
        self::assertTrue($journal->credits()[0]->amount()->equals(Money::ofYen(300_000)));
    }

    #[Test]
    public function 償却額ゼロの場合はnullを返す(): void
    {
        // 既に1円残価まで償却済
        $costYen = 1_200_000;
        $asset = new FixedAsset(
            id: 'fa-004',
            name: '機械装置D',
            acquisition: new Acquisition(
                cost: Money::ofYen($costYen),
                usefulLifeYears: 5,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );

        // 前期累計 = cost - 1 (1円残価まで償却済)
        $previousAccumulated = Money::ofYen($costYen - 1);

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: $previousAccumulated,
            mode: RoundingMode::Floor,
        );

        self::assertNull($journal);
    }

    #[Test]
    public function 期外取得で当期使用月数ゼロの場合はnullを返す(): void
    {
        // 取得日が期末より後 → 当期使用月数0
        $asset = new FixedAsset(
            id: 'fa-005',
            name: '機械装置E',
            acquisition: new Acquisition(
                cost: Money::ofYen(1_000_000),
                usefulLifeYears: 5,
                // 期末 2027-03-31 より後
                acquisitionDate: new \DateTimeImmutable('2027-04-01'),
            ),
            method: DepreciationMethodChoice::Straight,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        self::assertNull($journal);
    }

    #[Test]
    public function 平均償却で仕訳が生成できる(): void
    {
        $asset = new FixedAsset(
            id: 'fa-006',
            name: '備品F',
            acquisition: new Acquisition(
                cost: Money::ofYen(600_000),
                usefulLifeYears: 5,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::Average,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        self::assertNotNull($journal);
        self::assertTrue($journal->isBalanced());
        // 600,000 / (5 * 12) = 10,000/月 × 12ヶ月 = 120,000
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(120_000)));
    }

    #[Test]
    public function 級数法で仕訳が生成できる(): void
    {
        // 1年目: 1,200,000 * (5/15) = 400,000
        $asset = new FixedAsset(
            id: 'fa-007',
            name: '備品G',
            acquisition: new Acquisition(
                cost: Money::ofYen(1_200_000),
                usefulLifeYears: 5,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::SumOfYears,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
            yearIndex: 1,
        );

        self::assertNotNull($journal);
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(400_000)));
    }

    #[Test]
    public function 一括償却資産で仕訳が生成できる(): void
    {
        // 150,000 / 3 = 50,000 (floor)
        $asset = new FixedAsset(
            id: 'fa-008',
            name: '備品H',
            acquisition: new Acquisition(
                cost: Money::ofYen(150_000),
                usefulLifeYears: 3,
                acquisitionDate: new \DateTimeImmutable('2026-04-01'),
            ),
            method: DepreciationMethodChoice::LumpSumThreeYear,
            accountMapping: $this->makeMapping(),
        );

        $journal = FixedAssetJournalGenerator::generate(
            asset: $asset,
            period: $this->makePeriod(),
            previousAccumulated: Money::zero(),
            mode: RoundingMode::Floor,
        );

        self::assertNotNull($journal);
        self::assertTrue($journal->isBalanced());
    }
}
