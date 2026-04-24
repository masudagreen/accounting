<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;

/**
 * Repository port for {@see ApprovalToken}.
 *
 * Lookup by hash is the primary access path (capability URL → hash →
 * aggregate). Lookup by prefix exists solely for operator-initiated
 * resend / inspection flows — it MUST NOT be used to authenticate a
 * response.
 */
interface ApprovalTokenRepositoryInterface
{
    public function save(ApprovalToken $token): void;

    public function findByTokenHash(string $tokenHash): ?ApprovalToken;

    public function findByPrefix(string $tokenPrefix): ?ApprovalToken;

    /**
     * Marks every past-due, unresponded token as expired. Returns the number
     * of tokens that were expired during the call, so callers can surface the
     * count to operator CLIs.
     */
    public function expirePastDue(DateTimeImmutable $now): int;
}
