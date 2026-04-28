<?php

declare(strict_types=1);

namespace App\Compare\View;

/**
 * HTML 出力用のユーティリティ関数群.
 *
 * すべての出力は htmlspecialchars でエスケープする.
 */
final class HtmlHelper
{
    /**
     * HTML エスケープして文字列を返す.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * 数値を日本語通貨形式 (カンマ区切り) でフォーマットする.
     */
    public static function money(int $amount): string
    {
        return number_format($amount);
    }

    /**
     * 差額を色付きで返す HTML 文字列.
     * 0 なら緑チェック、非0なら赤で差額を表示する.
     */
    public static function diffBadge(int $diff): string
    {
        if ($diff === 0) {
            return '<span class="badge-ok">&#10003;</span>';
        }
        $sign = $diff > 0 ? '+' : '';
        return sprintf(
            '<span class="badge-ng">%s%s</span>',
            $sign,
            self::e(number_format($diff)),
        );
    }

    /**
     * ページ全体の HTML ラッパーを返す.
     *
     * @param string $title    ページタイトル
     * @param string $content  <body> 内のコンテンツ HTML
     * @param string $nav      ナビゲーション HTML (省略可)
     */
    public static function layout(string $title, string $content, string $nav = ''): string
    {
        $eTitle = self::e($title);
        return <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$eTitle} - Shadow Mode</title>
<link rel="stylesheet" href="/compare/assets/compare.css">
</head>
<body>
<div class="shadow-ribbon">Shadow Mode (Read-Only)</div>
<div class="container">
{$nav}
<h1>{$eTitle}</h1>
{$content}
</div>
</body>
</html>
HTML;
    }
}
