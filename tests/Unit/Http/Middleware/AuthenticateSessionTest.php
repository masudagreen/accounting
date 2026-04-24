<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Middleware;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Auth\ApiToken;
use Rucaro\Http\Middleware\AuthenticateSession;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Tests\Unit\Application\Support\FixedClock;
use Rucaro\Tests\Unit\Application\Support\InMemoryApiTokenRepo;

#[CoversClass(AuthenticateSession::class)]
final class AuthenticateSessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testReturnsNullWhenSessionIsAnonymous(): void
    {
        $auth = new AuthenticateSession(
            tokens: new InMemoryApiTokenRepo(),
            clock: new FixedClock(),
            session: new SessionStore(),
        );

        self::assertNull($auth->authenticate());
    }

    public function testReturnsUserIdForValidStoredToken(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();
        $plaintext = str_repeat('b', 64);
        $repo = new InMemoryApiTokenRepo();
        $repo->save(new ApiToken(
            id: '01HW7K9B2QV7C8Y4ZTOKEN00011',
            userId: '01HW7K9B2QV7C8Y4ZUSER000011',
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

        $session = new SessionStore();
        $session->setUser(
            userId: '01HW7K9B2QV7C8Y4ZUSER000011',
            plaintextToken: $plaintext,
            tokenId: '01HW7K9B2QV7C8Y4ZTOKEN00011',
            displayName: 'Taro',
            email: 'taro@example.com',
        );

        $auth = new AuthenticateSession($repo, $clock, $session);

        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000011', $auth->authenticate());
    }

    public function testClearsSessionWhenStoredTokenIsUnknown(): void
    {
        $clock = new FixedClock();
        $repo = new InMemoryApiTokenRepo();
        $session = new SessionStore();
        $session->setUser('U', str_repeat('c', 64), 'T', 'Name', 'e@e');

        $auth = new AuthenticateSession($repo, $clock, $session);

        self::assertNull($auth->authenticate());
        self::assertFalse(
            $session->isAuthenticated(),
            'Invalid stored tokens must be wiped so the user is forced to re-login.',
        );
    }

    public function testClearsSessionWhenStoredTokenIsExpired(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();
        $plaintext = str_repeat('d', 64);
        $repo = new InMemoryApiTokenRepo();
        $repo->save(new ApiToken(
            id: 'T1',
            userId: 'U1',
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
        $session = new SessionStore();
        $session->setUser('U1', $plaintext, 'T1', 'N', 'e@e');

        $auth = new AuthenticateSession($repo, $clock, $session);

        self::assertNull($auth->authenticate());
        self::assertFalse($session->isAuthenticated());
    }

    public function testRejectsSessionWhoseUserIdDoesNotMatchStoredToken(): void
    {
        $clock = new FixedClock();
        $now = $clock->getCurrentTime();
        $plaintext = str_repeat('e', 64);
        $repo = new InMemoryApiTokenRepo();
        $repo->save(new ApiToken(
            id: 'T1',
            userId: 'OWNER-OF-TOKEN',
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
        $session = new SessionStore();
        $session->setUser('ATTACKER', $plaintext, 'T1', 'N', 'e@e');

        $auth = new AuthenticateSession($repo, $clock, $session);

        self::assertNull($auth->authenticate());
        self::assertFalse($session->isAuthenticated());
    }
}
