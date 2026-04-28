<?php

declare(strict_types=1);

namespace App\Compare\View;

/**
 * 比較 UI のナビゲーションメニューを生成する.
 */
final class NavBuilder
{
    /**
     * @param int    $idEntity        現在の事業体 ID
     * @param int    $numFiscalPeriod 現在の会計期
     * @param string $currentPage     現在のページ名
     */
    public static function build(int $idEntity, int $numFiscalPeriod, string $currentPage = 'home'): string
    {
        $e = $idEntity;
        $p = $numFiscalPeriod;

        $links = [
            'home'          => ['label' => 'ホーム',     'href' => '/compare/'],
            'trial-balance' => ['label' => '試算表',     'href' => "/compare/?page=trial-balance&entity={$e}&period={$p}"],
            'profit-loss'   => ['label' => '損益計算書', 'href' => "/compare/?page=profit-loss&entity={$e}&period={$p}"],
            'balance-sheet' => ['label' => '貸借対照表', 'href' => "/compare/?page=balance-sheet&entity={$e}&period={$p}"],
            'journal-list'  => ['label' => '仕訳一覧',   'href' => "/compare/?page=journal-list&entity={$e}&period={$p}"],
        ];

        $items = '';
        foreach ($links as $key => $link) {
            $active = ($currentPage === $key) ? ' class="active"' : '';
            $label  = HtmlHelper::e($link['label']);
            $href   = HtmlHelper::e($link['href']);
            $items .= "<li{$active}><a href=\"{$href}\">{$label}</a></li>\n";
        }

        return "<nav><ul>\n{$items}</ul></nav>\n";
    }
}
