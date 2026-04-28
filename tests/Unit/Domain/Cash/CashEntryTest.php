<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Cash;

use App\Domain\Cash\CashDirection;
use App\Domain\Cash\CashEntry;
use App\Domain\Cash\CashEntryStatus;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CashEntry::class)]
#[CoversClass(CashDirection::class)]
#[CoversClass(CashEntryStatus::class)]
final class CashEntryTest extends TestCase
{
    private function makeEntry(
        CashDirection $direction = CashDirection::In,
        int $yen = 1000,
        CashEntryStatus $status = CashEntryStatus::Pending,
    ): CashEntry {
        return CashEntry::of(
            id: 'entry-001',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: $direction,
            amount: Money::ofYen($yen),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: 'テスト仕訳',
            status: $status,
        );
    }

    #[Test]
    public function 正常な入金エントリが生成できる(): void
    {
        $entry = $this->makeEntry(CashDirection::In, 1000);

        self::assertSame('entry-001', $entry->id());
        self::assertSame('2026-04-01', $entry->date()->format('Y-m-d'));
        self::assertSame(CashDirection::In, $entry->direction());
        self::assertTrue($entry->amount()->equals(Money::ofYen(1000)));
        self::assertSame('sales', $entry->counterAccountTitleId());
        self::assertSame('cash', $entry->cashAccountTitleId());
        self::assertSame('テスト仕訳', $entry->description());
        self::assertSame(CashEntryStatus::Pending, $entry->status());
    }

    #[Test]
    public function 正常な出金エントリが生成できる(): void
    {
        $entry = $this->makeEntry(CashDirection::Out, 500);

        self::assertSame(CashDirection::Out, $entry->direction());
        self::assertTrue($entry->amount()->equals(Money::ofYen(500)));
    }

    #[Test]
    public function 金額がゼロの場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('amount must be positive');

        CashEntry::of(
            id: 'e-002',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: CashDirection::In,
            amount: Money::zero(),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: null,
            status: CashEntryStatus::Pending,
        );
    }

    #[Test]
    public function 金額が負の場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('amount must be positive');

        CashEntry::of(
            id: 'e-003',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: CashDirection::In,
            amount: Money::ofYen(-100),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: null,
            status: CashEntryStatus::Pending,
        );
    }

    #[Test]
    public function descriptionがnullの場合も生成できる(): void
    {
        $entry = CashEntry::of(
            id: 'e-004',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: CashDirection::In,
            amount: Money::ofYen(100),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: null,
            status: CashEntryStatus::Pending,
        );

        self::assertNull($entry->description());
    }

    #[Test]
    public function Pendingステータスを新インスタンスでSettledに変更できる(): void
    {
        $entry = $this->makeEntry(status: CashEntryStatus::Pending);
        $settled = $entry->withStatus(CashEntryStatus::Settled);

        self::assertSame(CashEntryStatus::Pending, $entry->status());
        self::assertSame(CashEntryStatus::Settled, $settled->status());
        self::assertNotSame($entry, $settled);
    }

    #[Test]
    public function withStatusは元のエントリを変更しない(): void
    {
        $original = $this->makeEntry(status: CashEntryStatus::Pending);
        $original->withStatus(CashEntryStatus::Settled);

        self::assertSame(CashEntryStatus::Pending, $original->status());
    }

    #[Test]
    public function idが空文字の場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('id must not be empty');

        CashEntry::of(
            id: '',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: CashDirection::In,
            amount: Money::ofYen(100),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: null,
            status: CashEntryStatus::Pending,
        );
    }

    #[Test]
    public function CashDirectionのenumケースを確認できる(): void
    {
        self::assertSame('in', CashDirection::In->value);
        self::assertSame('out', CashDirection::Out->value);
    }

    #[Test]
    public function CashEntryStatusのenumケースを確認できる(): void
    {
        self::assertSame('pending', CashEntryStatus::Pending->value);
        self::assertSame('settled', CashEntryStatus::Settled->value);
    }
}
