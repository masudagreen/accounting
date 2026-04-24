<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Tests\Unit\Application\Support\FixedClock;

#[CoversClass(CsrfTokenManager::class)]
final class CsrfTokenManagerTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure $_SESSION is a clean slate per test; the production code uses
        // PHP's session superglobal, which we simulate in-memory here.
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testGenerateTokenStoresHexTokenInSession(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $token = $mgr->generateToken('ui_login');

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
        self::assertArrayHasKey(SessionStore::KEY_CSRF_TOKENS, $_SESSION);
    }

    public function testValidateTokenAcceptsFreshlyIssuedToken(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $token = $mgr->generateToken('ui_login');

        self::assertTrue($mgr->validateToken('ui_login', $token));
    }

    public function testValidateTokenIsSingleUse(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $token = $mgr->generateToken('ui_login');

        self::assertTrue($mgr->validateToken('ui_login', $token));
        self::assertFalse(
            $mgr->validateToken('ui_login', $token),
            'Tokens must not be replayable once accepted.',
        );
    }

    public function testValidateTokenRejectsEmptyString(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $mgr->generateToken('ui_login');

        self::assertFalse($mgr->validateToken('ui_login', ''));
    }

    public function testValidateTokenRejectsUnknownFormId(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $token = $mgr->generateToken('ui_login');

        self::assertFalse($mgr->validateToken('ui_logout', $token));
    }

    public function testValidateTokenRejectsTamperedToken(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $mgr->generateToken('ui_login');

        self::assertFalse($mgr->validateToken('ui_login', str_repeat('0', 64)));
    }

    public function testValidateTokenRejectsExpiredTokenAfterOneHour(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $token = $mgr->generateToken('ui_login');

        // Token TTL is 3600s; advance by 3601 so expiry branch fires.
        $clock->advance(CsrfTokenManager::TTL_SECONDS + 1);

        self::assertFalse($mgr->validateToken('ui_login', $token));
    }

    public function testTokensForDifferentFormsAreIndependent(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        $a = $mgr->generateToken('ui_login');
        $b = $mgr->generateToken('ui_logout');

        self::assertNotSame($a, $b);
        self::assertTrue($mgr->validateToken('ui_login', $a));
        self::assertTrue($mgr->validateToken('ui_logout', $b));
    }

    public function testMalformedSessionBagIsIgnored(): void
    {
        $clock = new FixedClock();
        $mgr = new CsrfTokenManager($clock);

        // Simulate a corrupt session bag written by some other code path.
        $_SESSION[SessionStore::KEY_CSRF_TOKENS] = 'not-an-array';

        self::assertFalse($mgr->validateToken('ui_login', 'whatever'));
    }
}
