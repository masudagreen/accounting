<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Domain\Report\HtmlHelper;
use App\Domain\Report\ReportFormat;

/**
 * 法人 株主資本等変動計算書 HTML レンダラー.
 *
 * 会社計算規則 様式第7号相当.
 */
final class CorporateSsRenderer
{
    public function render(CorporateSsData $data): string
    {
        $templatePath = $this->templatePath();
        return HtmlHelper::renderTemplate($templatePath, [
            'companyName' => $data->companyName,
            'period'      => $data->fiscalPeriod,
            'equity'      => $data->equity,
        ]);
    }

    public function format(): ReportFormat
    {
        return ReportFormat::Html;
    }

    private function templatePath(): string
    {
        return dirname(__DIR__, 4) . '/templates/reports/corporate-ss.html.php';
    }
}
