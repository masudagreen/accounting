<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Journal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Controller\Ui\Journal\JournalListController;

/**
 * Targets the pure helpers inside {@see JournalListController} that are
 * exposed for reuse / testability. The full controller dispatch is covered
 * by integration smoke tests against the running container.
 */
#[CoversClass(JournalListController::class)]
final class JournalListControllerTest extends TestCase
{
    public function testYearMonthToRangeReturnsNullsWhenYearIsMissing(): void
    {
        [$from, $to] = JournalListController::yearMonthToRange(null, 5);

        self::assertNull($from);
        self::assertNull($to);
    }

    public function testYearMonthToRangeYearOnlyReturnsFullYear(): void
    {
        [$from, $to] = JournalListController::yearMonthToRange(2025, null);

        self::assertNotNull($from);
        self::assertNotNull($to);
        self::assertSame('2025-01-01', $from->toPrimitive());
        self::assertSame('2025-12-31', $to->toPrimitive());
    }

    public function testYearMonthToRangeYearAndMonthReturnsMonthBounds(): void
    {
        [$from, $to] = JournalListController::yearMonthToRange(2025, 2);

        self::assertNotNull($from);
        self::assertNotNull($to);
        self::assertSame('2025-02-01', $from->toPrimitive());
        self::assertSame('2025-02-28', $to->toPrimitive());
    }

    public function testYearMonthToRangeOutOfRangeMonthFallsBackToFullYear(): void
    {
        [$from, $to] = JournalListController::yearMonthToRange(2024, 13);

        self::assertNotNull($from);
        self::assertNotNull($to);
        self::assertSame('2024-01-01', $from->toPrimitive());
        self::assertSame('2024-12-31', $to->toPrimitive());
    }
}
