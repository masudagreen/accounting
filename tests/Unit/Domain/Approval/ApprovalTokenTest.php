<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;

#[CoversClass(ApprovalToken::class)]
final class ApprovalTokenTest extends TestCase
{
    public function testIsExpiredReturnsTrueAtOrPastExpiry(): void
    {
        $token = $this->token('2026-04-21T12:00:00Z', '2026-04-21T13:00:00Z');
        self::assertFalse($token->isExpired(new DateTimeImmutable('2026-04-21T12:59:59Z', new DateTimeZone('UTC'))));
        self::assertTrue($token->isExpired(new DateTimeImmutable('2026-04-21T13:00:00Z', new DateTimeZone('UTC'))));
        self::assertTrue($token->isExpired(new DateTimeImmutable('2026-04-21T14:00:00Z', new DateTimeZone('UTC'))));
    }

    public function testIsRespondedReflectsRespondedAtField(): void
    {
        $active = $this->token('2026-04-21T12:00:00Z', '2026-04-24T12:00:00Z');
        self::assertFalse($active->isResponded());

        $responded = $active->respond(
            ApprovalDecision::Approved,
            'ok',
            new DateTimeImmutable('2026-04-22T00:00:00Z', new DateTimeZone('UTC')),
        );
        self::assertTrue($responded->isResponded());
    }

    public function testIsActiveRequiresUnrespondedAndUnexpired(): void
    {
        $now = new DateTimeImmutable('2026-04-22T00:00:00Z', new DateTimeZone('UTC'));
        $active = $this->token('2026-04-21T12:00:00Z', '2026-04-24T12:00:00Z');
        self::assertTrue($active->isActive($now));

        $responded = $active->respond(ApprovalDecision::Rejected, 'nope', $now);
        self::assertFalse($responded->isActive($now));

        $pastDue = new DateTimeImmutable('2026-04-25T00:00:00Z', new DateTimeZone('UTC'));
        self::assertFalse($active->isActive($pastDue));
    }

    public function testRespondReturnsNewInstanceWithoutMutatingOriginal(): void
    {
        $original = $this->token('2026-04-21T12:00:00Z', '2026-04-24T12:00:00Z');
        $at = new DateTimeImmutable('2026-04-22T01:00:00Z', new DateTimeZone('UTC'));
        $responded = $original->respond(ApprovalDecision::Approved, 'looks right', $at);

        self::assertNotSame($original, $responded);
        self::assertNull($original->respondedAt);
        self::assertNull($original->decision);
        self::assertSame('', $original->responseDetail);

        self::assertSame($at, $responded->respondedAt);
        self::assertSame(ApprovalDecision::Approved, $responded->decision);
        self::assertSame('looks right', $responded->responseDetail);
        self::assertSame($at, $responded->updatedAt);
    }

    public function testRespondPreservesImmutableMetadata(): void
    {
        $token = $this->token('2026-04-21T12:00:00Z', '2026-04-24T12:00:00Z');
        $responded = $token->respond(
            ApprovalDecision::Rejected,
            'missing receipt',
            new DateTimeImmutable('2026-04-22T02:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertSame($token->id, $responded->id);
        self::assertSame($token->tokenHash, $responded->tokenHash);
        self::assertSame($token->tokenPrefix, $responded->tokenPrefix);
        self::assertSame($token->channel, $responded->channel);
        self::assertSame($token->recipient, $responded->recipient);
        self::assertSame($token->issuedByUserId, $responded->issuedByUserId);
        self::assertSame($token->createdAt, $responded->createdAt);
    }

    private function token(string $issuedIso, string $expiresIso): ApprovalToken
    {
        $tz = new DateTimeZone('UTC');
        $issued = new DateTimeImmutable($issuedIso, $tz);
        $expires = new DateTimeImmutable($expiresIso, $tz);
        return new ApprovalToken(
            id: '01HW7K9B2QV7C8Y4ZAPPRVT00001',
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            tokenHash: str_repeat('a', 64),
            tokenPrefix: '0123456789abcdef',
            channel: ApprovalChannel::Email,
            recipient: 'reviewer@example.com',
            issuedAt: $issued,
            expiresAt: $expires,
            respondedAt: null,
            decision: null,
            responseDetail: '',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            createdAt: $issued,
            updatedAt: $issued,
        );
    }
}
