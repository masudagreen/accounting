<?php

declare(strict_types=1);

namespace App\Domain\Report;

/**
 * HTML 帳票出力の共通ユーティリティ.
 */
final class HtmlHelper
{
    /**
     * 金額を3桁カンマ区切りで表示する.
     * 負の値は△記号付きで表示する.
     */
    public static function money(int $amount): string
    {
        if ($amount < 0) {
            return '△' . number_format(abs($amount));
        }
        return number_format($amount);
    }

    /**
     * 文字列を HTML エスケープして返す.
     */
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * テンプレートファイルを読み込み、変数を展開して返す.
     *
     * @param array<string, mixed> $vars
     */
    public static function renderTemplate(string $templatePath, array $vars): string
    {
        if (! file_exists($templatePath)) {
            throw new \RuntimeException(sprintf('Template not found: %s', $templatePath));
        }
        extract($vars, EXTR_SKIP);
        ob_start();
        try {
            require $templatePath;
            return (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
