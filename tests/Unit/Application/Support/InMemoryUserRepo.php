<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Support;

use DateTimeImmutable;
use Rucaro\Domain\User\User;
use Rucaro\Domain\User\UserRepositoryInterface;

final class InMemoryUserRepo implements UserRepositoryInterface
{
    /** @var list<User> */
    public array $users = [];

    /** @var list<array{id: string, at: DateTimeImmutable}> */
    public array $touchLog = [];

    public function add(User $user): void
    {
        $this->users[] = $user;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $u) {
            if ($u->email === $email && !$u->isDeleted()) {
                return $u;
            }
        }
        return null;
    }

    public function findById(string $id): ?User
    {
        foreach ($this->users as $u) {
            if ($u->id === $id && !$u->isDeleted()) {
                return $u;
            }
        }
        return null;
    }

    public function touchLastLogin(string $id, DateTimeImmutable $at): void
    {
        $this->touchLog[] = ['id' => $id, 'at' => $at];
    }
}
