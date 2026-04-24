<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Auth;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Auth\LoginUseCase;
use Rucaro\Application\Auth\LoginUseCaseInput;
use Rucaro\Domain\Auth\InvalidCredentialsException;
use Rucaro\Domain\User\User;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Auth\PasswordHasher;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Unit\Application\Support\FixedClock;
use Rucaro\Tests\Unit\Application\Support\InMemoryApiTokenRepo;
use Rucaro\Tests\Unit\Application\Support\InMemoryUserRepo;

#[CoversClass(LoginUseCase::class)]
final class LoginUseCaseTest extends TestCase
{
    public function testValidCredentialsIssueAToken(): void
    {
        [$users, $tokens, $hasher, $clock] = $this->fixture();
        $useCase = new LoginUseCase(
            users: $users,
            tokens: $tokens,
            passwords: $hasher,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
            lifetimeSeconds: 3600,
        );

        $output = $useCase->execute(new LoginUseCaseInput(
            email: 'taro@example.com',
            password: 'correct-horse',
        ));

        self::assertSame(BearerTokenGenerator::TOKEN_HEX_LENGTH, strlen($output->token));
        self::assertSame(8, strlen($output->tokenPrefix));
        self::assertSame('taro@example.com', $output->email);
        self::assertSame(1, count($tokens->tokens));
        self::assertSame(hash('sha256', $output->token), $tokens->tokens[0]->tokenHash);
        self::assertGreaterThan($output->issuedAt, $output->expiresAt);
    }

    public function testUnknownEmailRaisesInvalidCredentials(): void
    {
        [$users, $tokens, $hasher, $clock] = $this->fixture();
        $useCase = new LoginUseCase(
            users: $users,
            tokens: $tokens,
            passwords: $hasher,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );

        $this->expectException(InvalidCredentialsException::class);

        $useCase->execute(new LoginUseCaseInput(
            email: 'nobody@example.com',
            password: 'correct-horse',
        ));
    }

    public function testWrongPasswordRaisesInvalidCredentials(): void
    {
        [$users, $tokens, $hasher, $clock] = $this->fixture();
        $useCase = new LoginUseCase(
            users: $users,
            tokens: $tokens,
            passwords: $hasher,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );

        $this->expectException(InvalidCredentialsException::class);

        $useCase->execute(new LoginUseCaseInput(
            email: 'taro@example.com',
            password: 'BAD-PASSWORD',
        ));
    }

    public function testInactiveUserCannotLogin(): void
    {
        [$users, $tokens, $hasher, $clock] = $this->fixture(isActive: false);
        $useCase = new LoginUseCase(
            users: $users,
            tokens: $tokens,
            passwords: $hasher,
            tokenGenerator: new BearerTokenGenerator(),
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );

        $this->expectException(InvalidCredentialsException::class);

        $useCase->execute(new LoginUseCaseInput(
            email: 'taro@example.com',
            password: 'correct-horse',
        ));
    }

    /**
     * @return array{0: InMemoryUserRepo, 1: InMemoryApiTokenRepo, 2: PasswordHasher, 3: FixedClock}
     */
    private function fixture(bool $isActive = true): array
    {
        $users = new InMemoryUserRepo();
        $tokens = new InMemoryApiTokenRepo();
        $hasher = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);
        $clock = new FixedClock();

        $users->add(new User(
            id: '01HW7K9B2QV7C8Y4ZUSER000001',
            loginId: 'taro',
            displayName: 'Taro Yamada',
            email: 'taro@example.com',
            passwordHash: $hasher->hash('correct-horse'),
            isActive: $isActive,
            lastLoginAt: null,
            createdAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-01T00:00:00Z'),
        ));
        return [$users, $tokens, $hasher, $clock];
    }
}
