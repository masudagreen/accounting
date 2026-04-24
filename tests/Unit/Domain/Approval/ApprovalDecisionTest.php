<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Approval;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Approval\ApprovalDecision;

#[CoversClass(ApprovalDecision::class)]
final class ApprovalDecisionTest extends TestCase
{
    public function testStringValuesMatchDbCheckConstraint(): void
    {
        self::assertSame('approved', ApprovalDecision::Approved->value);
        self::assertSame('rejected', ApprovalDecision::Rejected->value);
    }

    public function testTryFromRejectsUnknown(): void
    {
        self::assertNull(ApprovalDecision::tryFrom('pending'));
        self::assertNull(ApprovalDecision::tryFrom(''));
    }

    public function testIsApprovedDistinguishesFromRejected(): void
    {
        self::assertTrue(ApprovalDecision::Approved->isApproved());
        self::assertFalse(ApprovalDecision::Approved->isRejected());
        self::assertTrue(ApprovalDecision::Rejected->isRejected());
        self::assertFalse(ApprovalDecision::Rejected->isApproved());
    }
}
