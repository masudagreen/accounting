<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\CrSection;
use App\Domain\FinancialStatement\CostReport;
use App\Domain\FinancialStatement\CostReportBuilder;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Ledger\Ledger;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 製造原価報告書 (Cost Report).
 */
#[CoversClass(CostReport::class)]
#[CoversClass(CostReportBuilder::class)]
final class CostReportTest extends TestCase
{
    #[Test]
    public function 当期製造費用_3区分の合計(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('rawMaterials', 'cash', 500_000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('factoryWages', 'cash', 300_000)],
            ['date' => new \DateTimeImmutable('2026-04-03'), 'entry' => self::entry('factoryUtilities', 'cash', 100_000)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $cr = CostReportBuilder::build($tree, $tb);

        self::assertTrue($cr->materials()->equals(Money::ofYen(500_000)));
        self::assertTrue($cr->labor()->equals(Money::ofYen(300_000)));
        self::assertTrue($cr->manufacture()->equals(Money::ofYen(100_000)));
        self::assertTrue($cr->grossProductCost()->equals(Money::ofYen(900_000)));
    }

    #[Test]
    public function 当期製品製造原価_期首仕掛品を加え期末仕掛品を控除(): void
    {
        $tree = self::tree();
        $opening = OpeningBalances::of([
            'wipOpeningInventory' => Money::ofYen(100_000), // 期首仕掛品
        ]);
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('rawMaterials', 'cash', 500_000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('factoryWages', 'cash', 300_000)],
            ['date' => new \DateTimeImmutable('2026-04-03'), 'entry' => self::entry('factoryUtilities', 'cash', 100_000)],
            // 期末仕掛品計上
            ['date' => new \DateTimeImmutable('2027-03-31'), 'entry' => self::entry('wipClosingInventory', 'cash', 80_000)],
        ]);
        $tb = TrialBalance::build($tree, $opening, $ledger);
        $cr = CostReportBuilder::build($tree, $tb);

        // 当期製造費用 = 900,000
        self::assertTrue($cr->grossProductCost()->equals(Money::ofYen(900_000)));
        // 当期製品製造原価 = 900,000 + 100,000 - 80,000 - 0 = 920,000
        self::assertTrue($cr->currentWorkInProcess()->equals(Money::ofYen(920_000)));
    }

    #[Test]
    public function 他勘定振替も控除する(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('rawMaterials', 'cash', 1_000_000)],
            // 自家消費 (他勘定振替): 50,000
            ['date' => new \DateTimeImmutable('2027-02-01'), 'entry' => self::entry('wipOtherTransfer', 'cash', 50_000)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $cr = CostReportBuilder::build($tree, $tb);

        // 当期製造費用 = 1,000,000 (材料のみ)
        self::assertTrue($cr->grossProductCost()->equals(Money::ofYen(1_000_000)));
        // 当期製品製造原価 = 1,000,000 - 50,000 = 950,000
        self::assertTrue($cr->currentWorkInProcess()->equals(Money::ofYen(950_000)));
    }

    private static function tree(): AccountTree
    {
        return AccountTree::of([
            AccountTreeNode::leaf(AccountTitle::of('cash', '現金', AccountClassification::Asset)),
            AccountTreeNode::leaf(AccountTitle::of(
                'rawMaterials', '原材料費', AccountClassification::ManufacturingCost,
                crSection: CrSection::Materials,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'factoryWages', '工場給料', AccountClassification::ManufacturingCost,
                crSection: CrSection::Labor,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'factoryUtilities', '工場光熱費', AccountClassification::ManufacturingCost,
                crSection: CrSection::Manufacture,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'wipOpeningInventory', '期首仕掛品棚卸高', AccountClassification::ManufacturingCost,
                crSection: CrSection::OpeningWorkInProcess,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'wipClosingInventory', '期末仕掛品棚卸高', AccountClassification::ManufacturingCost,
                crSection: CrSection::ClosingWorkInProcess,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'wipOtherTransfer', '他勘定振替高', AccountClassification::ManufacturingCost,
                crSection: CrSection::RemoveTransfer,
            )),
        ]);
    }

    private static function entry(string $debitId, string $creditId, int $amount): JournalEntry
    {
        return JournalEntry::of(
            debits: [JournalLine::of($debitId, Money::ofYen($amount))],
            credits: [JournalLine::of($creditId, Money::ofYen($amount))],
        );
    }
}
