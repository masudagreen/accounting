<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Domain\Report\HtmlHelper;
use App\Domain\Report\ReportFormat;

/**
 * 法人 損益計算書 HTML レンダラー.
 *
 * 会社計算規則 様式第6号相当.
 */
final class CorporatePlRenderer
{
    public function render(CorporatePlData $data): string
    {
        $templatePath = $this->templatePath();
        return HtmlHelper::renderTemplate($templatePath, [
            'companyName' => $data->companyName,
            'period'      => $data->fiscalPeriod,
            'pl'          => $data->pl,
        ]);
    }

    public function format(): ReportFormat
    {
        return ReportFormat::Html;
    }

    private function templatePath(): string
    {
        return dirname(__DIR__, 4) . '/templates/reports/corporate-pl.html.php';
    }
}
