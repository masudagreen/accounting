<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Journal\JournalStatus;

#[CoversClass(JournalStatus::class)]
final class JournalStatusTest extends TestCase
{
    public function testValuesMatchDbCheckConstraint(): void
    {
        self::assertSame('draft', JournalStatus::Draft->value);
        self::assertSame('pending_approval', JournalStatus::PendingApproval->value);
        self::assertSame('approved', JournalStatus::Approved->value);
        self::assertSame('rejected', JournalStatus::Rejected->value);
        self::assertSame('posted', JournalStatus::Posted->value);
        self::assertSame('reversed', JournalStatus::Reversed->value);
        self::assertSame('voided', JournalStatus::Voided->value);
    }

    public function testFromDbStringReturnsMatchingCase(): void
    {
        self::assertSame(JournalStatus::Approved, JournalStatus::fromDbString('approved'));
        self::assertSame(JournalStatus::Posted, JournalStatus::fromDbString('posted'));
        self::assertSame(JournalStatus::Voided, JournalStatus::fromDbString('voided'));
    }

    public function testFromDbStringFallsBackToDraftOnUnknownInput(): void
    {
        self::assertSame(JournalStatus::Draft, JournalStatus::fromDbString('no_such_state'));
        self::assertSame(JournalStatus::Draft, JournalStatus::fromDbString(''));
    }

    #[DataProvider('terminalCases')]
    public function testIsTerminalFlagsReversedVoidedRejected(JournalStatus $status, bool $expected): void
    {
        self::assertSame($expected, $status->isTerminal());
    }

    /**
     * @return list<array{0: JournalStatus, 1: bool}>
     */
    public static function terminalCases(): array
    {
        return [
            [JournalStatus::Draft, false],
            [JournalStatus::PendingApproval, false],
            [JournalStatus::Approved, false],
            [JournalStatus::Posted, false],
            [JournalStatus::Rejected, true],
            [JournalStatus::Reversed, true],
            [JournalStatus::Voided, true],
        ];
    }

    public function testOnlyDraftIsMutable(): void
    {
        self::assertTrue(JournalStatus::Draft->isMutable());
        self::assertFalse(JournalStatus::Approved->isMutable());
        self::assertFalse(JournalStatus::Posted->isMutable());
    }
}
