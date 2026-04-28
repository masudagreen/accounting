<?php

declare(strict_types=1);

namespace App\Domain\Cash;

/**
 * 収支の方向.
 *
 *  - In:  入金 (現金/預金の増加)
 *  - Out: 出金 (現金/預金の減少)
 */
enum CashDirection: string
{
    case In  = 'in';
    case Out = 'out';
}
