<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateTimeZone;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Batch-expires every past-due approval token. Designed to be invoked from
 * the Phase 5 CLI (`bin/cowork approvals:expire`) or a cron job.
 *
 * The implementation defers the actual UPDATE to the repository; the
 * UseCase is a thin orchestration wrapper that threads the clock.
 */
final readonly class ExpirePastDueApprovalsUseCase
{
    public function __construct(
        private ApprovalTokenRepositoryInterface $tokens,
        private ClockInterface $clock,
    ) {
    }

    /**
     * Returns the number of tokens that transitioned from active to expired
     * during this invocation.
     */
    public function execute(): int
    {
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        return $this->tokens->expirePastDue($now);
    }
}
