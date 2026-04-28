<?php

declare(strict_types=1);

namespace App\Domain\Report\DetailedAccount;

use App\Domain\Report\HtmlHelper;
use App\Domain\Report\ReportFormat;

/**
 * 勘定科目内訳明細書 HTML レンダラー.
 *
 * 4種類の内訳書を個別メソッドで提供する.
 * 国税庁 別表「勘定科目内訳明細書」に準拠.
 */
final class DetailedAccountRenderer
{
    public function renderDeposits(DepositsBreakdown $data): string
    {
        return HtmlHelper::renderTemplate(
            $this->tpl('deposits.html.php'),
            ['data' => $data],
        );
    }

    public function renderReceivables(AccountsReceivableBreakdown $data): string
    {
        return HtmlHelper::renderTemplate(
            $this->tpl('accounts-receivable.html.php'),
            ['data' => $data],
        );
    }

    public function renderPayables(AccountsPayableBreakdown $data): string
    {
        return HtmlHelper::renderTemplate(
            $this->tpl('accounts-payable.html.php'),
            ['data' => $data],
        );
    }

    public function renderLoans(LoansPayableBreakdown $data): string
    {
        return HtmlHelper::renderTemplate(
            $this->tpl('loans-payable.html.php'),
            ['data' => $data],
        );
    }

    public function format(): ReportFormat
    {
        return ReportFormat::Html;
    }

    private function tpl(string $filename): string
    {
        return dirname(__DIR__, 4) . '/templates/reports/detailed-account/' . $filename;
    }
}
