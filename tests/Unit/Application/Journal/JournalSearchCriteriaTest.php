<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\JournalSearchCriteria;

#[CoversClass(JournalSearchCriteria::class)]
final class JournalSearchCriteriaTest extends TestCase
{
    public function testDefaultSortIsJournalDateDesc(): void
    {
        $criteria = new JournalSearchCriteria(entityId: 'E1');

        self::assertSame(JournalSearchCriteria::SORT_BY_JOURNAL_DATE, $criteria->sortBy);
        self::assertSame(JournalSearchCriteria::SORT_ORDER_DESC, $criteria->sortOrder);
    }

    public function testAcceptsAllAllowListedSortColumns(): void
    {
        foreach (JournalSearchCriteria::SORT_BY_ALLOW_LIST as $col) {
            $criteria = new JournalSearchCriteria(entityId: 'E1', sortBy: $col);
            self::assertSame($col, $criteria->sortBy);
        }
    }

    public function testRejectsUnknownSortColumn(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new JournalSearchCriteria(entityId: 'E1', sortBy: 'drop_table_users');
    }

    public function testRejectsUnknownSortOrder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new JournalSearchCriteria(entityId: 'E1', sortOrder: 'random');
    }

    public function testResolveSortFallsBackOnNullInputs(): void
    {
        [$by, $order] = JournalSearchCriteria::resolveSort(null, null);

        self::assertSame('journal_date', $by);
        self::assertSame('desc', $order);
    }

    public function testResolveSortFallsBackOnInjectionAttempts(): void
    {
        [$by, $order] = JournalSearchCriteria::resolveSort('id; DROP TABLE users', 'DESC; --');

        self::assertSame('journal_date', $by);
        self::assertSame('desc', $order);
    }

    public function testResolveSortNormalisesCaseForAllowListedValues(): void
    {
        [$by, $order] = JournalSearchCriteria::resolveSort('TOTAL_AMOUNT', 'ASC');

        self::assertSame('total_amount', $by);
        self::assertSame('asc', $order);
    }
}
