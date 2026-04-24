<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Approval\ExpirePastDueApprovalsUseCase;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryApprovalTokenRepository;

#[CoversClass(ExpirePastDueApprovalsUseCase::class)]
final class ExpirePastDueApprovalsUseCaseTest extends TestCase
{
    public function testReturnsZeroWhenNoTokensExist(): void
    {
        $repo = new InMemoryApprovalTokenRepository();
        $useCase = new ExpirePastDueApprovalsUseCase($repo, new FrozenClock());
        self::assertSame(0, $useCase->execute());
    }

    public function testCountsOnlyUnrespondedExpiredTokens(): void
    {
        $repo = new InMemoryApprovalTokenRepository();
        $repo->save($this->token('a', '2026-04-20T00:00:00Z', expired: true));
        $repo->save($this->token('b', '2026-04-19T00:00:00Z', expired: true));
        $repo->save($this->token('c', '2026-04-30T00:00:00Z', expired: false));

        $clock = new FrozenClock('2026-04-22T00:00:00.000Z');
        $useCase = new ExpirePastDueApprovalsUseCase($repo, $clock);

        self::assertSame(2, $useCase->execute());
    }

    private function token(string $seed, string $expiresIso, bool $expired): ApprovalToken
    {
        unset($expired);
        $tz = new DateTimeZone('UTC');
        $issued = new DateTimeImmutable('2026-04-18T00:00:00Z', $tz);
        $expires = new DateTimeImmutable($expiresIso, $tz);
        $hash = str_pad($seed, 64, '0');
        return new ApprovalToken(
            id: '01HW7K9B2QV7C8Y4ZAPPR00000' . $seed,
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            tokenHash: $hash,
            tokenPrefix: str_pad($seed, 16, '0'),
            channel: ApprovalChannel::Null,
            recipient: '',
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
