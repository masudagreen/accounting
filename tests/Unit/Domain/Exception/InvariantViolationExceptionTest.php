<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\DomainException;
use Rucaro\Domain\Exception\InvariantViolationException;

#[CoversClass(InvariantViolationException::class)]
final class InvariantViolationExceptionTest extends TestCase
{
    public function testExtendsDomainException(): void
    {
        $exception = InvariantViolationException::for('journal.must_balance');

        self::assertInstanceOf(DomainException::class, $exception);
    }

    public function testForBuildsMessageFromInvariantName(): void
    {
        $exception = InvariantViolationException::for('journal.must_balance');

        self::assertSame(
            "Domain invariant violated: 'journal.must_balance'.",
            $exception->getMessage(),
        );
    }

    public function testForSetsDomainCode(): void
    {
        $exception = InvariantViolationException::for('journal.must_balance');

        self::assertSame('INVARIANT_VIOLATION', $exception->domainCode());
    }

    public function testForPropagatesContext(): void
    {
        $context = ['journalId' => 'j-1', 'debit' => 100, 'credit' => 50];

        $exception = InvariantViolationException::for(
            'journal.must_balance',
            $context,
        );

        self::assertSame(
            array_merge($context, ['invariant' => 'journal.must_balance']),
            $exception->context(),
        );
    }
}
