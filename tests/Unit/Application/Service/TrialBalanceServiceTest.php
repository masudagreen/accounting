<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\TrialBalanceService;
use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
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
 * TrialBalanceService のユニットテスト.
 */
final class TrialBalanceServiceTest extends TestCase
{
    private JournalRepository&MockObject $journalRepo;
    private AccountTreeRepository&MockObject $treeRepo;
    private TrialBalanceService $service;

    protected function setUp(): void
    {
        $this->journalRepo = $this->createMock(JournalRepository::class);
        $this->treeRepo    = $this->createMock(AccountTreeRepository::class);
        $this->service     = new TrialBalanceService($this->journalRepo, $this->treeRepo);
    }

    private function makeSimpleTree(): AccountTree
    {
        $cash  = AccountTitle::of('cash', '現金', AccountClassification::Asset);
        $sales = AccountTitle::of('sales', '売上高', AccountClassification::Revenue);

        return AccountTree::of([
            AccountTreeNode::leaf($cash),
            AccountTreeNode::leaf($sales),
        ]);
    }

    public function testBuildReturnsTrialBalanceDtoWithCorrectStructure(): void
    {
        // Arrange
        $tree = $this->makeSimpleTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);

        $debit  = JournalLine::of('cash', Money::ofYen(50000));
        $credit = JournalLine::of('sales', Money::ofYen(50000));
        $entry  = JournalEntry::of([$debit], [$credit]);
        $date   = new DateTimeImmutable('2024-04-01');

        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([
            ['date' => $date, 'entry' => $entry],
        ]);

        // Act
        $dto = $this->service->build(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: OpeningBalances::empty(),
        );

        // Assert — DTO は array<string, mixed>
        $this->assertArrayHasKey('rows', $dto);
        $this->assertIsArray($dto['rows']);
    }

    public function testBuildRowContainsExpectedFields(): void
    {
        // Arrange
        $tree = $this->makeSimpleTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);
        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([]);

        // Act
        $dto = $this->service->build(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: OpeningBalances::empty(),
        );

        // Assert — 各行に必須フィールドがあること
        $rows = $dto['rows'];
        self::assertIsArray($rows);
        self::assertNotEmpty($rows);
        $firstRow = reset($rows);
        self::assertIsArray($firstRow);
        self::assertArrayHasKey('id', $firstRow);
        self::assertArrayHasKey('title', $firstRow);
        self::assertArrayHasKey('opening', $firstRow);
        self::assertArrayHasKey('periodDebits', $firstRow);
        self::assertArrayHasKey('periodCredits', $firstRow);
        self::assertArrayHasKey('closing', $firstRow);
    }

    public function testBuildWithOpeningBalanceReflectsInClosingBalance(): void
    {
        // Arrange
        $tree = $this->makeSimpleTree();
        $this->treeRepo->method('loadCombinedTree')->willReturn($tree);
        $this->journalRepo->method('findByEntityAndPeriod')->willReturn([]);

        $opening = OpeningBalances::of(['cash' => Money::ofYen(100000)]);

        // Act
        $dto = $this->service->build(
            idEntity: 1,
            numFiscalPeriod: 1,
            opening: $opening,
        );

        // Assert — cash の期首が反映されている
        $cashRow = $dto['rows']['cash'] ?? null;
        $this->assertNotNull($cashRow);
        $this->assertSame(100000, $cashRow['opening']);
        $this->assertSame(100000, $cashRow['closing']);
    }
}
