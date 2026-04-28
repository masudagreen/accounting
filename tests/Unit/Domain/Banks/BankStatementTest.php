<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banks;

use App\Domain\Banks\BankStatement;
use App\Domain\Cash\CashDirection;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankStatement::class)]
final class BankStatementTest extends TestCase
{
    #[Test]
    public function 正常な入金明細が生成できる(): void
    {
        $stmt = new BankStatement(
            date: new \DateTimeImmutable('2026-04-15'),
            description: '振込入金',
            amount: Money::ofYen(100_000),
            direction: CashDirection::In,
            balanceAfter: Money::ofYen(500_000),
        );

        self::assertSame('2026-04-15', $stmt->date()->format('Y-m-d'));
        self::assertSame('振込入金', $stmt->description());
        self::assertTrue($stmt->amount()->equals(Money::ofYen(100_000)));
        self::assertSame(CashDirection::In, $stmt->direction());
        self::assertTrue($stmt->balanceAfter()->equals(Money::ofYen(500_000)));
    }

    #[Test]
    public function 正常な出金明細が生成できる(): void
    {
        $stmt = new BankStatement(
            date: new \DateTimeImmutable('2026-04-20'),
            description: '公共料金引落',
            amount: Money::ofYen(15_000),
            direction: CashDirection::Out,
            balanceAfter: Money::ofYen(485_000),
        );

        self::assertSame(CashDirection::Out, $stmt->direction());
        self::assertTrue($stmt->amount()->equals(Money::ofYen(15_000)));
    }

    #[Test]
    public function amountはゼロの場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('amount must be positive');

        new BankStatement(
            date: new \DateTimeImmutable('2026-04-01'),
            description: '明細',
            amount: Money::zero(),
            direction: CashDirection::In,
            balanceAfter: Money::ofYen(100_000),
        );
    }

    #[Test]
    public function amountが負の場合は例外が発生する(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('amount must be positive');

        new BankStatement(
            date: new \DateTimeImmutable('2026-04-01'),
            description: '明細',
            amount: Money::ofYen(-1),
            direction: CashDirection::In,
            balanceAfter: Money::ofYen(100_000),
        );
    }

    #[Test]
    public function descriptionが空文字でも生成できる(): void
    {
        $stmt = new BankStatement(
            date: new \DateTimeImmutable('2026-04-01'),
            description: '',
            amount: Money::ofYen(1),
            direction: CashDirection::In,
            balanceAfter: Money::ofYen(1),
        );

        self::assertSame('', $stmt->description());
    }
}
