<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compare;

use App\Compare\Auth\SessionAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * SessionAuthenticator のユニットテスト.
 *
 * PDO は Stub で差し替え、DB 接続なしで認証ロジックを検証する.
 */
final class SessionAuthenticatorTest extends TestCase
{
    // ─── authenticate() ────────────────────────────────────────────────

    public function testAuthenticateReturnsFalseWhenCookieKeyAbsent(): void
    {
        $pdo           = $this->createPdoStub(null);
        $authenticator = new SessionAuthenticator($pdo);

        $result = $authenticator->authenticate([]);

        self::assertFalse($result);
    }

    public function testAuthenticateReturnsFalseWhenCookieValueEmpty(): void
    {
        $pdo           = $this->createPdoStub(null);
        $authenticator = new SessionAuthenticator($pdo);

        $result = $authenticator->authenticate(['id' => '']);

        self::assertFalse($result);
    }

    public function testAuthenticateReturnsFalseWhenSessionNotFound(): void
    {
        $pdo           = $this->createPdoStub(null);
        $authenticator = new SessionAuthenticator($pdo);

        $result = $authenticator->authenticate(['id' => 'no-such-session']);

        self::assertFalse($result);
    }

    public function testAuthenticateReturnsTrueWhenSessionFoundAndFresh(): void
    {
        $sessionRow = [
            'stampRegister' => (string) (time() - 100),
            'ip'            => '127.0.0.1',
            'idCookie'      => 'abc123',
            'idAccount'     => '42',
            'flagAPI'       => '0',
        ];

        $pdo           = $this->createPdoStub($sessionRow);
        $authenticator = new SessionAuthenticator($pdo, maxAgeSeconds: 3600);

        $result = $authenticator->authenticate(['id' => 'abc123']);

        self::assertTrue($result);
    }

    public function testAuthenticateReturnsFalseWhenSessionExpired(): void
    {
        $sessionRow = [
            'stampRegister' => (string) (time() - 7200),
            'ip'            => '127.0.0.1',
            'idCookie'      => 'expired-token',
            'idAccount'     => '1',
            'flagAPI'       => '0',
        ];

        $pdo           = $this->createPdoStub($sessionRow);
        $authenticator = new SessionAuthenticator($pdo, maxAgeSeconds: 3600);

        $result = $authenticator->authenticate(['id' => 'expired-token']);

        self::assertFalse($result);
    }

    public function testAuthenticateReturnsFalseForApiSession(): void
    {
        $sessionRow = [
            'stampRegister' => (string) (time() - 100),
            'ip'            => '127.0.0.1',
            'idCookie'      => 'api-token',
            'idAccount'     => '1',
            'flagAPI'       => '1',
        ];

        $pdo           = $this->createPdoStub($sessionRow);
        $authenticator = new SessionAuthenticator($pdo);

        $result = $authenticator->authenticate(['id' => 'api-token']);

        self::assertFalse($result);
    }

    // ─── getAccountId() ─────────────────────────────────────────────────

    public function testGetAccountIdReturnsNullBeforeAuthentication(): void
    {
        $pdo           = $this->createPdoStub(null);
        $authenticator = new SessionAuthenticator($pdo);

        self::assertNull($authenticator->getAccountId());
    }

    public function testGetAccountIdReturnsIdAfterSuccessfulAuthentication(): void
    {
        $sessionRow = [
            'stampRegister' => (string) (time() - 100),
            'ip'            => '127.0.0.1',
            'idCookie'      => 'token-x',
            'idAccount'     => '99',
            'flagAPI'       => '0',
        ];

        $pdo           = $this->createPdoStub($sessionRow);
        $authenticator = new SessionAuthenticator($pdo);

        $authenticator->authenticate(['id' => 'token-x']);

        self::assertSame(99, $authenticator->getAccountId());
    }

    // ─── helpers ────────────────────────────────────────────────────────

    /**
     * PDO スタブを生成する.
     * セッション行が null の場合は fetch() が false を返すよう設定する.
     *
     * @param array<string, string>|null $sessionRow
     */
    private function createPdoStub(?array $sessionRow): \PDO
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn($sessionRow ?? false);

        $pdoMock = $this->createMock(\PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        return $pdoMock;
    }
}
