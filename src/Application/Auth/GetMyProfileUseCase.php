<?php

declare(strict_types=1);

namespace Rucaro\Application\Auth;

use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\User\User;
use Rucaro\Domain\User\UserRepositoryInterface;

/**
 * Fetch the authenticated user's profile by id. The HTTP middleware resolves
 * the Bearer token into a user id before invoking this use case.
 */
final readonly class GetMyProfileUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {
    }

    public function execute(string $userId): User
    {
        $user = $this->users->findById($userId);
        if ($user === null) {
            throw EntityNotFoundException::for('User', $userId);
        }
        return $user;
    }
}
