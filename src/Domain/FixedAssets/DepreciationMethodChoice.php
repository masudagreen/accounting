<?php

declare(strict_types=1);

namespace App\Domain\FixedAssets;

/**
 * 減価償却方法の選択肢.
 *
 *  - Straight:        定額法 (平成19年4月1日以降取得)
 *  - Declining200:    200%定率法 (平成24年4月1日以降取得)
 *  - Declining250:    250%定率法 (平成19年4月1日〜平成24年3月31日取得)
 *  - SumOfYears:      級数法
 *  - Average:         平均償却
 *  - Voluntary:       任意償却
 *  - LumpSumThreeYear: 一括償却資産 (3年均等)
 */
enum DepreciationMethodChoice: string
{
    case Straight        = 'straight';
    case Declining200    = 'declining200';
    case Declining250    = 'declining250';
    case SumOfYears      = 'sum_of_years';
    case Average         = 'average';
    case Voluntary       = 'voluntary';
    case LumpSumThreeYear = 'lump_sum_three_year';
}
