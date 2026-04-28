<?php

declare(strict_types=1);

namespace App\Compare\Routing;

/**
 * compare/ ディレクトリの簡易ルーター.
 *
 * ?page=xxx クエリパラメータをページ名に解決する.
 * 未知のページ名・空値は 'home' にフォールバックする.
 * 全て GET のみ. POST は受け付けない.
 */
final class Router
{
    /** サポートするページ名の一覧. */
    private const array VALID_PAGES = [
        'home',
        'trial-balance',
        'profit-loss',
        'balance-sheet',
        'journal-list',
    ];

    /**
     * クエリパラメータ配列からページ名を解決する.
     *
     * @param array<string, mixed> $queryParams $_GET の内容
     */
    public function resolve(array $queryParams): string
    {
        $page = trim((string) ($queryParams['page'] ?? ''));

        if ($page === '' || ! in_array($page, self::VALID_PAGES, true)) {
            return 'home';
        }

        return $page;
    }
}
