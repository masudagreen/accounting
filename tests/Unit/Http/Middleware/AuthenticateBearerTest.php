<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Middleware;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Auth\ApiToken;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Tests\Unit\Application\Support\FixedClock;
use Rucaro\Tests\Unit\Application\Support\InMemoryApiTokenRepo;

#[CoversClass(AuthenticateBearer::class)]
final class AuthenticateBearerTest extends TestCase
{
    public function testExtractBearerHandlesVariousCasings(): void
    {
        self::assertSame('abc', AuthenticateBearer::extractBearer('Bearer abc'));
        self::assertSame('abc', AuthenticateBearer::extractBearer('bearer abc'));
        self::assertSame('abc', AuthenticateBearer::extractBearer('BEARER abc'));
        self::assertNull(AuthenticateBearer::extractBearer(''));
        self::assertNull(AuthenticateBearer::extractBearer(null));
        self::assertNull(AuthenticateBearer::extractBearer('Basic whatever'));
        self::assertNull(AuthenticateBearer::extractBearer('Bearer    '));
    }

    public function testAuthenticateReturnsUserIdForValidToken(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();

        $repo = new InMemoryApiTokenRepo();
        $plaintext = str_repeat('a', 64);
        $repo->save(new ApiToken(
            id: '01HW7K9B2QV7C8Y4ZTOKEN00001',
            userId: '01HW7K9B2QV7C8Y4ZUSER000001',
            tokenHash: BearerTokenGenerator::hash($plaintext),
            tokenPrefix: substr($plaintext, 0, 8),
            scopes: '',
            issuedAt: $now,
            expiresAt: $now->modify('+1 hour'),
            revokedAt: null,
            lastUsedAt: null,
            createdAt: $now,
            updatedAt: $now,
        ));

        $auth = new AuthenticateBearer($repo, $clock);

        self::assertSame(
            '01HW7K9B2QV7C8Y4ZUSER000001',
            $auth->authenticate('Bearer ' . $plaintext),
        );
    }

    public function testAuthenticateRejectsUnknownToken(): void
    {
        $clock = new FixedClock();
        $repo = new InMemoryApiTokenRepo();
        $auth = new AuthenticateBearer($repo, $clock);

        self::assertNull($auth->authenticate('Bearer unknown-token'));
    }

    public function testAuthenticateRejectsExpiredToken(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();

        $repo = new InMemoryApiTokenRepo();
        $plaintext = str_repeat('b', 64);
        $repo->save(new ApiToken(
            id: '01HW7K9B2QV7C8Y4ZTOKEN00002',
            userId: '01HW7K9B2QV7C8Y4ZUSER000002',
            tokenHash: BearerTokenGenerator::hash($plaintext),
            tokenPrefix: substr($plaintext, 0, 8),
            scopes: '',
            issuedAt: $now->modify('-2 hour'),
            expiresAt: $now->modify('-1 hour'),
            revokedAt: null,
            lastUsedAt: null,
            createdAt: $now->modify('-2 hour'),
            updatedAt: $now->modify('-2 hour'),
        ));

        $auth = new AuthenticateBearer($repo, $clock);

        self::assertNull($auth->authenticate('Bearer ' . $plaintext));
    }

    public function testAuthenticateRejectsRevokedToken(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();

        $repo = new InMemoryApiTokenRepo();
        $plaintext = str_repeat('c', 64);
        $repo->save(new ApiToken(
            id: '01HW7K9B2QV7C8Y4ZTOKEN00003',
            userId: '01HW7K9B2QV7C8Y4ZUSER000003',
            tokenHash: BearerTokenGenerator::hash($plaintext),
            tokenPrefix: substr($plaintext, 0, 8),
            scopes: '',
            issuedAt: $now->modify('-30 minutes'),
            expiresAt: $now->modify('+30 minutes'),
            revokedAt: $now->modify('-5 minutes'),
            lastUsedAt: null,
            createdAt: $now->modify('-30 minutes'),
            updatedAt: $now->modify('-5 minutes'),
        ));

        $auth = new AuthenticateBearer($repo, $clock);

        self::assertNull($auth->authenticate('Bearer ' . $plaintext));
    }
}
