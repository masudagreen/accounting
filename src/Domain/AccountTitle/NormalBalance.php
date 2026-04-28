<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 通常残高方向。
 *
 * 元実装の `flagDebit`:
 *   1 → Debit (借方残高: 資産・費用)
 *   0 → Credit (貸方残高: 負債・純資産・収益)
 */
enum NormalBalance: string
{
    case Debit = 'debit';
    case Credit = 'credit';
}
