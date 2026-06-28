<?php

declare(strict_types=1);

namespace Rucaro\Domain\User;

/**
 * Application user aggregate.
 *
 * Stays intentionally small — the reference implementation only needs what the
 * auth endpoints and journal-creation flow use. Further fields can be added as
 * Phase 4 grows.
 */
final readonly class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLERK = 'clerk';

    public function __construct(
        public string $id,
        public string $loginId,
        public string $displayName,
        public string $email,
        public string $passwordHash,
        public bool $isActive,
        public ?\DateTimeImmutable $lastLoginAt,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public ?\DateTimeImmutable $deletedAt = null,
        public string $role = self::ROLE_ADMIN,
    ) {
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
