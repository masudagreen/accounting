<?php

declare(strict_types=1);

namespace App\Domain\Report\BlueReturn;

use App\Domain\Report\HtmlHelper;
use App\Domain\Report\ReportFormat;

/**
 * 青色申告決算書 HTML レンダラー.
 *
 * 令和7年版 (2026年提出分) 様式に準拠.
 * 損益計算書 (一般用) と 貸借対照表 を1枚の HTML に出力する.
 */
final class BlueReturnRenderer
{
    public function render(BlueReturnData $data): string
    {
        $templatePath = $this->templatePath();
        return HtmlHelper::renderTemplate($templatePath, [
            'businessName' => $data->businessName,
            'period'       => $data->fiscalPeriod,
            'pl'           => $data->pl,
            'bs'           => $data->bs,
        ]);
    }

    public function format(): ReportFormat
    {
        return ReportFormat::Html;
    }

    private function templatePath(): string
    {
        return dirname(__DIR__, 4) . '/templates/reports/blue-return-pl-general.html.php';
    }
}
