<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateTimeZone;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Resolves a plaintext token into the approval record + target snapshot so
 * the reviewer can see what they're about to approve / reject.
 *
 * Does NOT consume the token — it stays active for the subsequent
 * {@see RespondToApprovalUseCase} call.
 */
final readonly class FindApprovalByTokenUseCase
{
    public function __construct(
        private ApprovalTokenRepositoryInterface $tokens,
        private ApprovalTargetResolverInterface $targets,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $tokenPlaintext): FindApprovalByTokenUseCaseOutput
    {
        $hash = BearerTokenGenerator::hash($tokenPlaintext);
        $token = $this->tokens->findByTokenHash($hash);
        if ($token === null) {
            throw TokenNotFoundException::forPlaintext();
        }
        $target = $this->targets->resolve($token->targetKind, $token->targetId);
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $status = self::computeStatus($token, $now);

        return new FindApprovalByTokenUseCaseOutput(
            token: $token,
            target: $target,
            status: $status,
        );
    }

    private static function computeStatus(ApprovalToken $token, \DateTimeImmutable $now): string
    {
        if ($token->isResponded()) {
            return 'responded';
        }
        if ($token->isExpired($now)) {
            return 'expired';
        }
        return 'active';
    }
}
