<?php

declare(strict_types=1);

namespace Rucaro\Domain\User;

use DateTimeImmutable;

/**
 * Application user aggregate.
 *
 * Stays intentionally small — the reference implementation only needs what the
 * auth endpoints and journal-creation flow use. Further fields can be added as
 * Phase 4 grows.
 */
final readonly class User
{
    public function __construct(
        public string $id,
        public string $loginId,
        public string $displayName,
        public string $email,
        public string $passwordHash,
        public bool $isActive,
        public ?DateTimeImmutable $lastLoginAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
