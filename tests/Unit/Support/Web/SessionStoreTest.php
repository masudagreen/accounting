<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Web\SessionStore;

#[CoversClass(SessionStore::class)]
final class SessionStoreTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testSetUserPopulatesEveryAccessor(): void
    {
        $store = new SessionStore();
        $store->setUser(
            userId:         '01HW7K9B2QV7C8Y4ZUSER000099',
            plaintextToken: str_repeat('a', 64),
            tokenId:        '01HW7K9B2QV7C8Y4ZTOKEN00099',
            displayName:    '山田 太郎',
            email:          'taro@example.com',
        );

        self::assertTrue($store->isAuthenticated());
        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000099', $store->getUserId());
        self::assertSame(str_repeat('a', 64), $store->getTokenPlaintext());
        self::assertSame('01HW7K9B2QV7C8Y4ZTOKEN00099', $store->getTokenId());
        self::assertSame('山田 太郎', $store->getDisplayName());
        self::assertSame('taro@example.com', $store->getEmail());
    }

    public function testAnonymousSessionReportsNotAuthenticated(): void
    {
        $store = new SessionStore();

        self::assertFalse($store->isAuthenticated());
        self::assertNull($store->getUserId());
        self::assertNull($store->getTokenPlaintext());
    }

    public function testSelectedEntityAndFiscalTermRoundTrip(): void
    {
        $store = new SessionStore();
        $store->setSelectedEntity('ENT-ABC');
        $store->setSelectedFiscalTerm('FT-2026');

        self::assertSame('ENT-ABC', $store->getSelectedEntity());
        self::assertSame('FT-2026', $store->getSelectedFiscalTerm());
    }

    public function testForgetUserClearsCredentialKeysButLeavesOtherKeys(): void
    {
        $store = new SessionStore();
        $store->setUser('U', 'T', 'TID', 'name', 'e@e');
        $_SESSION['unrelated_key'] = 'keep-me';

        $store->forgetUser();

        self::assertFalse($store->isAuthenticated());
        self::assertSame('keep-me', $_SESSION['unrelated_key']);
    }

    public function testEmptyStringsAreTreatedAsMissing(): void
    {
        $_SESSION[SessionStore::KEY_USER_ID] = '';
        $_SESSION[SessionStore::KEY_TOKEN] = '';
        $_SESSION[SessionStore::KEY_SELECTED_ENTITY] = '';

        $store = new SessionStore();

        self::assertNull($store->getUserId());
        self::assertNull($store->getTokenPlaintext());
        self::assertNull($store->getSelectedEntity());
    }
}
