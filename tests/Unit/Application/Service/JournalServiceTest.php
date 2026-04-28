<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\JournalService;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Money\Money;
use App\Infrastructure\Persistence\JournalRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * JournalService のユニットテスト.
 *
 * 外部依存 (JournalRepository) はモックに差し替え、サービスの振る舞いのみを検証する.
 */
final class JournalServiceTest extends TestCase
{
    private JournalRepository&MockObject $repo;
    private JournalService $service;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(JournalRepository::class);
        $this->service = new JournalService($this->repo);
    }

    public function testGetEntriesReturnsEmptyArrayWhenNoJournalsExist(): void
    {
        // Arrange
        $this->repo
            ->method('findByEntityAndPeriod')
            ->willReturn([]);

        // Act
        $result = $this->service->getEntries(idEntity: 1, numFiscalPeriod: 1);

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetEntriesReturnsMappedDtoArrayForEachJournalEntry(): void
    {
        // Arrange
        $debitLine  = JournalLine::of('cash', Money::ofYen(10000));
        $creditLine = JournalLine::of('sales', Money::ofYen(10000));
        $entry      = JournalEntry::of([$debitLine], [$creditLine]);
        $date       = new DateTimeImmutable('2024-04-01');

        $this->repo
            ->method('findByEntityAndPeriod')
            ->willReturn([
                ['date' => $date, 'entry' => $entry],
            ]);

        // Act
        $result = $this->service->getEntries(idEntity: 1, numFiscalPeriod: 1);

        // Assert
        $this->assertCount(1, $result);
        $row = $result[0];
        $this->assertSame('2024-04-01', $row['date']);
        $this->assertSame(10000, $row['totalDebits']);
        $this->assertSame(10000, $row['totalCredits']);
        $this->assertIsArray($row['debits']);
        $this->assertIsArray($row['credits']);
    }

    public function testGetEntriesPassesEntityAndPeriodToRepository(): void
    {
        // Arrange
        $this->repo
            ->expects($this->once())
            ->method('findByEntityAndPeriod')
            ->with(42, 7)
            ->willReturn([]);

        // Act
        $this->service->getEntries(idEntity: 42, numFiscalPeriod: 7);
    }

    public function testGetEntriesTotalDebitsCoverMultiLineEntry(): void
    {
        // Arrange
        $line1 = JournalLine::of('cash', Money::ofYen(3000));
        $line2 = JournalLine::of('bank', Money::ofYen(7000));
        $credit = JournalLine::of('sales', Money::ofYen(10000));
        $entry  = JournalEntry::of([$line1, $line2], [$credit]);
        $date   = new DateTimeImmutable('2024-04-10');

        $this->repo
            ->method('findByEntityAndPeriod')
            ->willReturn([['date' => $date, 'entry' => $entry]]);

        // Act
        $result = $this->service->getEntries(idEntity: 1, numFiscalPeriod: 1);

        // Assert
        $this->assertSame(10000, $result[0]['totalDebits']);
        $this->assertCount(2, $result[0]['debits']);
    }
}
