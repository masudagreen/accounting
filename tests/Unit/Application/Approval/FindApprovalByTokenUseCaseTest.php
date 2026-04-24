<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\FindApprovalByTokenUseCase;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Tests\Support\Fake\FakeApprovalTarget;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;
use Rucaro\Tests\Support\Fake\StubApprovalTargetResolver;

#[CoversClass(FindApprovalByTokenUseCase::class)]
final class FindApprovalByTokenUseCaseTest extends TestCase
{
    public function testActiveTokenIsReturnedWithActiveStatus(): void
    {
        [$useCase, $plaintext] = $this->wire(status: 'active');
        $output = $useCase->execute($plaintext);
        self::assertSame('active', $output->status);
        self::assertSame(ApprovalTargetKind::Journal, $output->target->kind());
    }

    public function testExpiredTokenExposesExpiredStatus(): void
    {
        [$useCase, $plaintext] = $this->wire(status: 'expired');
        $output = $useCase->execute($plaintext);
        self::assertSame('expired', $output->status);
    }

    public function testRespondedTokenExposesRespondedStatus(): void
    {
        [$useCase, $plaintext] = $this->wire(status: 'responded');
        $output = $useCase->execute($plaintext);
        self::assertSame('responded', $output->status);
    }

    public function testUnknownTokenRaisesTokenNotFound(): void
    {
        [$useCase] = $this->wire(status: 'active');
        $this->expectException(TokenNotFoundException::class);
        $useCase->execute('deadbeef' . str_repeat('0', 56));
    }

    /**
     * @return array{0:FindApprovalByTokenUseCase,1:string}
     */
    private function wire(string $status): array
    {
        $tz = new DateTimeZone('UTC');
        $clock = new FrozenClock('2026-04-22T00:00:00.000Z');
        $plaintext = str_repeat('a', 64);
        $hash = BearerTokenGenerator::hash($plaintext);
        $issued = new DateTimeImmutable('2026-04-20T00:00:00Z', $tz);
        $expires = $status === 'expired'
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
            respondedAt: $status === 'responded' ? $issued : null,
            decision: $status === 'responded' ? ApprovalDecision::Approved : null,
            responseDetail: '',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            createdAt: $issued,
            updatedAt: $issued,
        );
        $repo = new InMemoryApprovalTokenRepository();
        $repo->save($token);

        $resolver = new StubApprovalTargetResolver();
        $resolver->register(new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        ));

        $useCase = new FindApprovalByTokenUseCase($repo, $resolver, $clock);
        return [$useCase, $plaintext];
    }
}
