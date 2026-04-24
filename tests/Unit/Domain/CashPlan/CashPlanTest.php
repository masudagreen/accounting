<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\CashPlan;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(CashPlan::class)]
#[CoversClass(CashPlanEntry::class)]
#[CoversClass(CashPlanCategory::class)]
final class CashPlanTest extends TestCase
{
    public function testMonthlyDeltaNetsInflowsAgainstOutflows(): void
    {
        $plan = $this->buildPlan([
            $this->entry(CashPlanCategory::OperatingIn, '売上入金', [
                '1000000.0000', '0', '0', '0', '0', '0',
                '0', '0', '0', '0', '0', '0',
            ]),
            $this->entry(CashPlanCategory::OperatingOut, '給与', [
                '300000.0000', '0', '0', '0', '0', '0',
                '0', '0', '0', '0', '0', '0',
            ]),
        ]);
        self::assertSame('700000.0000', $plan->monthlyDelta(1));
        self::assertSame('0.0000', $plan->monthlyDelta(2));
    }

    public function testClosingBalanceStacksFromOpening(): void
    {
        $plan = $this->buildPlan([
            $this->entry(CashPlanCategory::OperatingIn, '売上', [
                '100000.0000', '200000.0000', '300000.0000', '0', '0', '0',
                '0', '0', '0', '0', '0', '0',
            ]),
        ], '500000.0000');
        self::assertSame('600000.0000', $plan->closingBalance(1));
        self::assertSame('800000.0000', $plan->closingBalance(2));
        self::assertSame('1100000.0000', $plan->closingBalance(3));
    }

    public function testTotalsByCategory(): void
    {
        $plan = $this->buildPlan([
            $this->entry(CashPlanCategory::OperatingIn, '売上', [
                '100', '100', '100', '100', '100', '100',
                '100', '100', '100', '100', '100', '100',
            ]),
            $this->entry(CashPlanCategory::FinancingIn, '借入', [
                '50', '0', '0', '0', '0', '0',
                '0', '0', '0', '0', '0', '0',
            ]),
        ]);
        $totals = $plan->totalsByCategory();
        self::assertSame('1200.0000', $totals['operating_in']);
        self::assertSame('50.0000', $totals['financing_in']);
        self::assertSame('0.0000', $totals['operating_out']);
    }

    public function testRejectsInvalidName(): void
    {
        $this->expectException(ValidationException::class);
        $this->buildPlan([], '0', '');
    }

    public function testRejectsNonAsciiCurrencyCode(): void
    {
        $this->expectException(ValidationException::class);
        $this->buildPlan([], '0', 'Plan A', 'jp1');
    }

    public function testCategorySignMatchesInflowDirection(): void
    {
        self::assertSame(1, CashPlanCategory::OperatingIn->sign());
        self::assertSame(-1, CashPlanCategory::OperatingOut->sign());
        self::assertTrue(CashPlanCategory::FinancingIn->isInflow());
        self::assertFalse(CashPlanCategory::FinancingOut->isInflow());
        self::assertSame('operating', CashPlanCategory::OperatingOut->group());
    }

    /**
     * @param list<CashPlanEntry> $entries
     */
    private function buildPlan(
        array $entries = [],
        string $openingBalance = '0.0000',
        string $name = 'Plan A',
        string $currency = 'JPY',
    ): CashPlan {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new CashPlan(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAAC',
            name: $name,
            openingBalance: $openingBalance,
            currencyCode: $currency,
            notes: null,
            entries: $entries,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAAD',
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null,
        );
    }

    /**
     * @param list<string> $amounts
     */
    private function entry(CashPlanCategory $category, string $label, array $amounts): CashPlanEntry
    {
        return new CashPlanEntry(
            id: '01HAAAAAAAAAAAAAAAAAAAAA' . chr(ord('0') + count($amounts) % 10) . 'A',
            cashPlanId: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            category: $category,
            label: $label,
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
    }
}
