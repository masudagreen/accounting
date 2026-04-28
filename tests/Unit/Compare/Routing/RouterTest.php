<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compare\Routing;

use App\Compare\Routing\Router;
use PHPUnit\Framework\TestCase;

/**
 * Router のユニットテスト.
 *
 * URL クエリパラメータ ?page=xxx を対応するページ名に解決することを検証する.
 */
final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testResolveDefaultsToHomeWhenPageParamAbsent(): void
    {
        self::assertSame('home', $this->router->resolve([]));
    }

    public function testResolveHomePage(): void
    {
        self::assertSame('home', $this->router->resolve(['page' => 'home']));
    }

    public function testResolveTrialBalancePage(): void
    {
        self::assertSame('trial-balance', $this->router->resolve(['page' => 'trial-balance']));
    }

    public function testResolveProfitLossPage(): void
    {
        self::assertSame('profit-loss', $this->router->resolve(['page' => 'profit-loss']));
    }

    public function testResolveBalanceSheetPage(): void
    {
        self::assertSame('balance-sheet', $this->router->resolve(['page' => 'balance-sheet']));
    }

    public function testResolveJournalListPage(): void
    {
        self::assertSame('journal-list', $this->router->resolve(['page' => 'journal-list']));
    }

    public function testResolveUnknownPageFallsBackToHome(): void
    {
        self::assertSame('home', $this->router->resolve(['page' => 'nonexistent-page']));
    }

    public function testResolveIgnoresCaseDifferences(): void
    {
        // page キーは小文字のみ受け付ける。大文字混じりは不明ページ扱い → home
        self::assertSame('home', $this->router->resolve(['page' => 'Trial-Balance']));
    }

    public function testResolveStripsLeadingAndTrailingWhitespace(): void
    {
        // trim 後の値が有効ページ名であれば解決する
        self::assertSame('trial-balance', $this->router->resolve(['page' => '  trial-balance  ']));
    }

    public function testResolveIgnoresEmptyPageParam(): void
    {
        self::assertSame('home', $this->router->resolve(['page' => '']));
    }
}
