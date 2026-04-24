<?php

declare(strict_types=1);

namespace Rucaro\Domain\User;

/**
 * Repository port for {@see User}. Implementations live in the Infrastructure
 * layer; the domain depends only on this interface.
 */
interface UserRepositoryInterface
{
    /**
     * @return User|null Null when no user with this email exists or the user
     *                   is soft-deleted.
     */
    public function findByEmail(string $email): ?User;

    /**
     * @return User|null Null when no user with this id exists or the user
     *                   is soft-deleted.
     */
    public function findById(string $id): ?User;

    /**
     * Record a successful login for audit purposes.
     */
    public function touchLastLogin(string $id, \DateTimeImmutable $at): void;
}
