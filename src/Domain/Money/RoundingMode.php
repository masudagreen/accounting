<?php

declare(strict_types=1);

namespace App\Domain\Money;

/**
 * 端数処理モード。
 *
 * 元実装の `flagFractionDep` 等で使われる文字列値:
 *  - 'floor' = 切捨
 *  - 'ceil'  = 切上
 *  - 'round' = 四捨五入 (HALF_AWAY_FROM_ZERO)
 */
enum RoundingMode: string
{
    case Floor = 'floor';
    case Ceil = 'ceil';
    case Round = 'round';
}
