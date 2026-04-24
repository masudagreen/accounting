<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Auth;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Auth\GetMyProfileUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\User\User;
use Rucaro\Tests\Unit\Application\Support\InMemoryUserRepo;

#[CoversClass(GetMyProfileUseCase::class)]
final class GetMyProfileUseCaseTest extends TestCase
{
    public function testReturnsTheUserWhenFound(): void
    {
        $repo = new InMemoryUserRepo();
        $repo->add(new User(
            id: '01HW7K9B2QV7C8Y4ZUSER000001',
            loginId: 'taro',
            displayName: 'Taro',
            email: 'taro@example.com',
            passwordHash: 'argon2id$...',
            isActive: true,
            lastLoginAt: null,
            createdAt: new DateTimeImmutable('2026-04-01'),
            updatedAt: new DateTimeImmutable('2026-04-01'),
        ));

        $useCase = new GetMyProfileUseCase($repo);

        self::assertSame('taro@example.com', $useCase->execute('01HW7K9B2QV7C8Y4ZUSER000001')->email);
    }

    public function testRaisesEntityNotFoundOnMissingId(): void
    {
        $useCase = new GetMyProfileUseCase(new InMemoryUserRepo());

        $this->expectException(EntityNotFoundException::class);

        $useCase->execute('01HW7K9B2QV7C8Y4ZUSER000999');
    }
}
