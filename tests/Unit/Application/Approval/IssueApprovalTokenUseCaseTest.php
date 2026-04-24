<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Approval;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCaseInput;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FakeApprovalTarget;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;
use Rucaro\Tests\Support\Fake\RecordingApprovalNotifier;
use Rucaro\Tests\Support\Fake\StubApprovalTargetResolver;

#[CoversClass(IssueApprovalTokenUseCase::class)]
final class IssueApprovalTokenUseCaseTest extends TestCase
{
    public function testIssueGeneratesPlaintextAndPersistsHash(): void
    {
        $repo = new InMemoryApprovalTokenRepository();
        $resolver = new StubApprovalTargetResolver();
        $resolver->register(new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        ));
        $notifier = new RecordingApprovalNotifier();
        $clock = new FrozenClock();

        $useCase = new IssueApprovalTokenUseCase(
            tokens: $repo,
            targets: $resolver,
            notifier: $notifier,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );

        $output = $useCase->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            channel: ApprovalChannel::Email,
            recipient: 'reviewer@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
        ));

        self::assertSame(64, strlen($output->tokenPlaintext));
        self::assertSame(16, strlen($output->tokenPrefix));
        self::assertSame(ApprovalChannel::Email, $output->channel);
        self::assertCount(1, $repo->byHash);
        self::assertSame(BearerTokenGenerator::hash($output->tokenPlaintext), array_key_first($repo->byHash));
    }

    public function testIssueHonoursCustomTtl(): void
    {
        [$useCase] = $this->wire();
        $output = $useCase->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            channel: ApprovalChannel::Email,
            recipient: 'r@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            ttlHours: 12,
        ));

        // FrozenClock default is 2026-04-21T12:00:00Z → +12h = 2026-04-22T00:00:00Z
        self::assertSame('2026-04-22T00:00:00+00:00', $output->expiresAt->format('Y-m-d\TH:i:sP'));
    }

    public function testIssueFailsWhenTargetDoesNotExist(): void
    {
        [$useCase] = $this->wire();
        $this->expectException(EntityNotFoundException::class);
        $useCase->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: 'MISSING',
            channel: ApprovalChannel::Email,
            recipient: 'r@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
        ));
    }

    public function testIssueNotifiesViaNotifierWithPlaintext(): void
    {
        [$useCase, , , $notifier] = $this->wire();
        $output = $useCase->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            channel: ApprovalChannel::Email,
            recipient: 'r@example.com',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
        ));

        self::assertCount(1, $notifier->calls);
        self::assertSame($output->tokenPlaintext, $notifier->calls[0]['plaintext']);
        self::assertSame(ApprovalTargetKind::Journal, $notifier->calls[0]['target']->kind());
    }

    /**
     * @return array{0:IssueApprovalTokenUseCase,1:InMemoryApprovalTokenRepository,2:StubApprovalTargetResolver,3:RecordingApprovalNotifier,4:FrozenClock}
     */
    private function wire(): array
    {
        $repo = new InMemoryApprovalTokenRepository();
        $resolver = new StubApprovalTargetResolver();
        $resolver->register(new FakeApprovalTarget(
            ApprovalTargetKind::Journal,
            '01HW7K9B2QV7C8Y4ZJRNL000001',
        ));
        $notifier = new RecordingApprovalNotifier();
        $clock = new FrozenClock();
        $useCase = new IssueApprovalTokenUseCase(
            tokens: $repo,
            targets: $resolver,
            notifier: $notifier,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );
        return [$useCase, $repo, $resolver, $notifier, $clock];
    }
}
