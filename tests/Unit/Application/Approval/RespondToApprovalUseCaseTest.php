<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\RespondToApprovalUseCase;
use Rucaro\Application\Approval\RespondToApprovalUseCaseInput;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\Exception\AlreadyRespondedException;
use Rucaro\Domain\Approval\Exception\TokenExpiredException;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Tests\Support\Fake\FakeApprovalTarget;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;
use Rucaro\Tests\Support\Fake\StubApprovalTargetResolver;

#[CoversClass(RespondToApprovalUseCase::class)]
final class RespondToApprovalUseCaseTest extends TestCase
{
    public function testApprovalCallsApplyApprovalAndMarksTokenResponded(): void
    {
        $fixture = $this->fixture();
        $output = $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Approved,
            responseDetail: 'looks good',
            actorUserId: '01HW7K9B2QV7C8Y4ZUSER000099',
        ));

        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000099', $fixture['target']->approvedBy);
        self::assertNotNull($fixture['target']->approvedAt);
        self::assertSame(ApprovalDecision::Approved, $output->token->decision);
        self::assertSame('looks good', $output->token->responseDetail);
        self::assertNotNull($output->token->respondedAt);
    }

    public function testRejectionCallsApplyRejectionWithReason(): void
    {
        $fixture = $this->fixture();
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Rejected,
            responseDetail: 'missing receipt',
        ));

        self::assertSame('missing receipt', $fixture['target']->rejectReason);
        self::assertNotNull($fixture['target']->rejectedAt);
        self::assertNull($fixture['target']->approvedBy);
    }

    public function testSecondResponseRaisesAlreadyResponded(): void
    {
        $fixture = $this->fixture();
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Approved,
            actorUserId: '01HW7K9B2QV7C8Y4ZUSER000099',
        ));

        $this->expectException(AlreadyRespondedException::class);
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Rejected,
        ));
    }

    public function testExpiredTokenRaisesTokenExpired(): void
    {
        $fixture = $this->fixture(expired: true);
        $this->expectException(TokenExpiredException::class);
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Approved,
        ));
    }

    public function testUnknownPlaintextRaisesTokenNotFound(): void
    {
        $fixture = $this->fixture();
        $this->expectException(TokenNotFoundException::class);
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: str_repeat('f', 64),
            decision: ApprovalDecision::Approved,
        ));
    }

    public function testEmptyActorFallsBackToIssuer(): void
    {
        $fixture = $this->fixture();
        $fixture['useCase']->execute(new RespondToApprovalUseCaseInput(
            tokenPlaintext: $fixture['plaintext'],
            decision: ApprovalDecision::Approved,
            actorUserId: '',
        ));
        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000001', $fixture['target']->approvedBy);
    }

    /**
     * @return array{useCase:RespondToApprovalUseCase, plaintext:string, repo:InMemoryApprovalTokenRepository, target:FakeApprovalTarget, clock:FrozenClock}
     */
    private function fixture(bool $expired = false): array
    {
        $tz = new DateTimeZone('UTC');
        $clock = new FrozenClock('2026-04-22T00:00:00.000Z');
        $plaintext = str_repeat('a', 64);
        $hash = BearerTokenGenerator::hash($plaintext);
        $issued = new DateTimeImmutable('2026-04-20T00:00:00Z', $tz);
        $expires = $expired
            ? new DateTimeImmutable('2026-04-21T00:00:00Z', $tz)
            : new DateTimeImmutable('2026-04-25T00:00:00Z', $tz);

        $token = new ApprovalToken(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000001',
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            tokenHash: $hash,
            tokenPrefix: substr($plaintext, 0, 16),
            channel: ApprovalChannel::Email,
            recipient: 'r@example.com',
            issuedAt: $issued,
            expiresAt: $expires,
            respondedAt: null,
            decision: null,
            responseDetail: '',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            createdAt: $issued,
            updatedAt: $issued,
        );

        $repo = new InMemoryApprovalTokenRepository();
        $repo->save($token);

        $target = new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        );
        $resolver = new StubApprovalTargetResolver();
        $resolver->register($target);

        $useCase = new RespondToApprovalUseCase($repo, $resolver, $clock);

        return [
            'useCase'   => $useCase,
            'plaintext' => $plaintext,
            'repo'      => $repo,
            'target'    => $target,
            'clock'     => $clock,
        ];
    }
}
