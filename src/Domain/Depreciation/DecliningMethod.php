<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

/**
 * 定率法の改定種別.
 *
 *  - TwoHundredFiftyPercent: 250%定率法 (平成19年4月1日〜平成24年3月31日取得)
 *  - TwoHundredPercent:      200%定率法 (平成24年4月1日以降取得)
 */
enum DecliningMethod: string
{
    case TwoHundredFiftyPercent = '250';
    case TwoHundredPercent      = '200';
}
