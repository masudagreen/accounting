<?php

declare(strict_types=1);

namespace App\Infrastructure\Pdf;

/**
 * HTML → PDF 変換インターフェース.
 */
interface PdfRenderer
{
    /**
     * HTML 文字列を PDF バイナリに変換する.
     *
     * @param string $html UTF-8 エンコードされた HTML 文字列
     * @return string PDF バイナリ
     */
    public function render(string $html): string;
}
