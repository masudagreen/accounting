<?php

declare(strict_types=1);

namespace App\Domain\Report;

/**
 * 帳票の出力フォーマット.
 */
enum ReportFormat: string
{
    case Html = 'html';
    case Pdf  = 'pdf';
    case Csv  = 'csv';
}
