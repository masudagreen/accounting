<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\FinancialStatementService;
use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\PlSection;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\OpeningBalances;
use App\Infrastructure\Persistence\AccountTreeRepository;
use App\Infrastructure\Persistence\JournalRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * FinancialStatementService のユニットテスト.
 */
final class FinancialStatementServiceTest extends TestCase
{
    private JournalRepository&MockObject $journalRepo;
    private AccountTreeRepository&MockObject $treeRepo;
    private FinancialStatementService $service;

    protected function setUp(): void
    {
        $this->journalRepo = $this->createMock(JournalRepository::class);
        $this->treeRepo    = $this->createMock(AccountTreeRepository::class);
        $this->service     = new FinancialStatementService($this->journalRepo, $this->treeRepo);
    }

    private function makePlTree(): AccountTree
    {
        $cash  = AccountTitle::of('cash',  '現金',   AccountClassification::Asset);
        $sales = AccountTitle::of('sales', '売上高', AccountClassification::Revenue, null, false, PlSection::Sales);
        $cogs  = AccountTitle::of('cogs',  '売上原価', AccountClassification::Expense, null, false, PlSection::CostOfSales);

        return AccountTree::of([
            AccountTreeNode::leaf($cash),
            AccountTreeNode::leaf($sales),
            AccountTreeNode::leaf($cogs),
        ]);
    }

    public function testBuildProfitAndLossReturnsDtoWithSalesAndCost(): void
    {
        // Arrange
        $tree = $this->makePlTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);

        $d1 = JournalLine::of('cash', Money::ofYen(100000));
        $c1 = JournalLine::of('sales', Money::ofYen(100000));

        $d2 = JournalLine::of('cogs', Money::ofYen(60000));
        $c2 = JournalLine::of('cash', Money::ofYen(60000));

        $date = new DateTimeImmutable('2024-04-01');
        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([
            ['date' => $date, 'entry' => JournalEntry::of([$d1], [$c1])],
            ['date' => $date, 'entry' => JournalEntry::of([$d2], [$c2])],
        ]);

        // Act
        $dto = $this->service->buildProfitAndLoss(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: OpeningBalances::empty(),
        );

        // Assert
// assertIsArray removed (already typed as array)
        $this->assertArrayHasKey('sales', $dto);
        $this->assertArrayHasKey('costOfSales', $dto);
        $this->assertArrayHasKey('grossProfit', $dto);
        $this->assertArrayHasKey('netIncome', $dto);
        $this->assertSame(100000, $dto['sales']);
        $this->assertSame(60000, $dto['costOfSales']);
        $this->assertSame(40000, $dto['grossProfit']);
    }

    public function testBuildBalanceSheetReturnsDtoWithAssetAndLiability(): void
    {
        // Arrange
        $tree = $this->makePlTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);
        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([]);

        $opening = OpeningBalances::of(['cash' => Money::ofYen(200000)]);

        // Act
        $dto = $this->service->buildBalanceSheet(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: $opening,
        );

        // Assert
// assertIsArray removed (already typed as array)
        $this->assertArrayHasKey('totalAssets', $dto);
        $this->assertArrayHasKey('totalLiabilities', $dto);
        $this->assertArrayHasKey('totalEquity', $dto);
        $this->assertSame(200000, $dto['totalAssets']);
    }

    public function testBuildProfitAndLossWithNoDataReturnsZeros(): void
    {
        // Arrange
        $tree = $this->makePlTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);
        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([]);

        // Act
        $dto = $this->service->buildProfitAndLoss(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: OpeningBalances::empty(),
        );

        // Assert
        $this->assertSame(0, $dto['sales']);
        $this->assertSame(0, $dto['netIncome']);
    }
}
