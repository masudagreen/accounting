<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Approval;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Approval\ApprovalChannel;

#[CoversClass(ApprovalChannel::class)]
final class ApprovalChannelTest extends TestCase
{
    public function testStringValuesMatchDbCheckConstraint(): void
    {
        self::assertSame('email', ApprovalChannel::Email->value);
        self::assertSame('line', ApprovalChannel::Line->value);
        self::assertSame('slack', ApprovalChannel::Slack->value);
        self::assertSame('discord', ApprovalChannel::Discord->value);
        self::assertSame('null', ApprovalChannel::Null->value);
    }

    public function testTryFromAcceptsAllKnownChannels(): void
    {
        foreach (['email', 'line', 'slack', 'discord', 'null'] as $raw) {
            self::assertNotNull(ApprovalChannel::tryFrom($raw));
        }
    }

    public function testTryFromRejectsUnknownChannel(): void
    {
        self::assertNull(ApprovalChannel::tryFrom('sms'));
        self::assertNull(ApprovalChannel::tryFrom(''));
    }

    public function testIsMailOnlyTrueForEmail(): void
    {
        self::assertTrue(ApprovalChannel::Email->isMail());
        self::assertFalse(ApprovalChannel::Line->isMail());
        self::assertFalse(ApprovalChannel::Null->isMail());
    }

    public function testIsMessagingOnlyTrueForChatPlatforms(): void
    {
        self::assertFalse(ApprovalChannel::Email->isMessaging());
        self::assertTrue(ApprovalChannel::Line->isMessaging());
        self::assertTrue(ApprovalChannel::Slack->isMessaging());
        self::assertTrue(ApprovalChannel::Discord->isMessaging());
        self::assertFalse(ApprovalChannel::Null->isMessaging());
    }

    public function testIsNoopOnlyTrueForNull(): void
    {
        self::assertTrue(ApprovalChannel::Null->isNoop());
        self::assertFalse(ApprovalChannel::Email->isNoop());
    }
}
