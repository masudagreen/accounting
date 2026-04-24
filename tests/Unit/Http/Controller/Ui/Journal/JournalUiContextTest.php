<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Journal;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Controller\Ui\Journal\JournalUiContext;

/**
 * Targets the pure static defaulting logic in {@see JournalUiContext}. The
 * PDO-backed lookups are exercised indirectly by the integration tests
 * against the running container.
 */
#[CoversClass(JournalUiContext::class)]
final class JournalUiContextTest extends TestCase
{
    public function testDefaultFiscalTermIdReturnsNullWhenNoTerms(): void
    {
        $now = new DateTimeImmutable('2025-06-01', new DateTimeZone('UTC'));

        self::assertNull(JournalUiContext::defaultFiscalTermId([], $now));
    }

    public function testDefaultFiscalTermIdPrefersTermContainingNow(): void
    {
        $now = new DateTimeImmutable('2025-06-01', new DateTimeZone('UTC'));
        $terms = [
            ['id' => 'FT1', 'fiscalPeriod' => 1, 'startDate' => '2024-01-01', 'endDate' => '2024-12-31'],
            ['id' => 'FT2', 'fiscalPeriod' => 2, 'startDate' => '2025-01-01', 'endDate' => '2025-12-31'],
        ];

        self::assertSame('FT2', JournalUiContext::defaultFiscalTermId($terms, $now));
    }

    public function testDefaultFiscalTermIdFallsBackToFirstTermWhenNoneMatch(): void
    {
        $now = new DateTimeImmutable('2030-06-01', new DateTimeZone('UTC'));
        $terms = [
            ['id' => 'FT_LATEST', 'fiscalPeriod' => 3, 'startDate' => '2026-01-01', 'endDate' => '2026-12-31'],
            ['id' => 'FT_OLD',    'fiscalPeriod' => 2, 'startDate' => '2025-01-01', 'endDate' => '2025-12-31'],
        ];

        self::assertSame('FT_LATEST', JournalUiContext::defaultFiscalTermId($terms, $now));
    }
}
