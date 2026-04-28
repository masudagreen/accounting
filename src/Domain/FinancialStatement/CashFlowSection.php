<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

/**
 * キャッシュフロー計算書の3区分.
 */
enum CashFlowSection: string
{
    /** 営業活動によるキャッシュフロー */
    case Operating = 'operating';

    /** 投資活動によるキャッシュフロー */
    case Investing = 'investing';

    /** 財務活動によるキャッシュフロー */
    case Financing = 'financing';
}
