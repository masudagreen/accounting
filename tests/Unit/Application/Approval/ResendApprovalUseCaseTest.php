<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCase;
use Rucaro\Application\Approval\ResendApprovalUseCase;
use Rucaro\Application\Approval\ResendApprovalUseCaseInput;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FakeApprovalTarget;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;
use Rucaro\Tests\Support\Fake\RecordingApprovalNotifier;
use Rucaro\Tests\Support\Fake\StubApprovalTargetResolver;

#[CoversClass(ResendApprovalUseCase::class)]
final class ResendApprovalUseCaseTest extends TestCase
{
    public function testResendIssuesFreshTokenWhenOldOneStillActive(): void
    {
        $fixture = $this->fixture();
        $output = $fixture['resend']->execute(new ResendApprovalUseCaseInput(
            tokenPrefix: $fixture['prefix'],
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000002',
        ));
        self::assertSame(ApprovalChannel::Email, $output->channel);
        self::assertNotSame('', $output->tokenPlaintext);
        self::assertCount(2, $fixture['repo']->byHash);
        self::assertCount(1, $fixture['notifier']->calls);
    }

    public function testResendOnUnknownPrefixRaisesTokenNotFound(): void
    {
        $fixture = $this->fixture();
        $this->expectException(TokenNotFoundException::class);
        $fixture['resend']->execute(new ResendApprovalUseCaseInput(
            tokenPrefix: 'missing_prefix123',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000002',
        ));
    }

    /**
     * @return array{resend:ResendApprovalUseCase, prefix:string, repo:InMemoryApprovalTokenRepository, notifier:RecordingApprovalNotifier}
     */
    private function fixture(): array
    {
        $tz = new DateTimeZone('UTC');
        $clock = new FrozenClock('2026-04-22T00:00:00.000Z');

        $plaintext = str_repeat('a', 64);
        $hash = BearerTokenGenerator::hash($plaintext);
        $issued = new DateTimeImmutable('2026-04-21T00:00:00Z', $tz);
        $expires = new DateTimeImmutable('2026-04-25T00:00:00Z', $tz);

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

        $resolver = new StubApprovalTargetResolver();
        $resolver->register(new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        ));

        $notifier = new RecordingApprovalNotifier();
        $issue = new IssueApprovalTokenUseCase(
            tokens: $repo,
            targets: $resolver,
            notifier: $notifier,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );
        $resend = new ResendApprovalUseCase($repo, $issue, $resolver, $clock);

        return [
            'resend'   => $resend,
            'prefix'   => $token->tokenPrefix,
            'repo'     => $repo,
            'notifier' => $notifier,
        ];
    }
}
