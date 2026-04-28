<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Domain\Report\HtmlHelper;
use App\Domain\Report\ReportFormat;

/**
 * 法人 貸借対照表 HTML レンダラー.
 *
 * 会社計算規則 様式第5号相当.
 */
final class CorporateBsRenderer
{
    public function render(CorporateBsData $data): string
    {
        $templatePath = $this->templatePath();
        return HtmlHelper::renderTemplate($templatePath, [
            'companyName' => $data->companyName,
            'period'      => $data->fiscalPeriod,
            'bs'          => $data,
        ]);
    }

    public function format(): ReportFormat
    {
        return ReportFormat::Html;
    }

    private function templatePath(): string
    {
        return dirname(__DIR__, 4) . '/templates/reports/corporate-bs.html.php';
    }
}
