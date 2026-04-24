<?php

declare(strict_types=1);

namespace Rucaro\Application\Auth;

use DateTimeImmutable;

final readonly class LoginUseCaseOutput
{
    public function __construct(
        public string $token,
        public string $tokenPrefix,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public string $userId,
        public string $loginId,
        public string $displayName,
        public string $email,
    ) {
    }
}
