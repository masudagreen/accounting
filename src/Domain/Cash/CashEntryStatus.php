<?php

declare(strict_types=1);

namespace App\Domain\Cash;

/**
 * 収支エントリの消込ステータス.
 *
 *  - Pending: 未消込 (仕訳確定前)
 *  - Settled: 消込済 (仕訳確定後)
 */
enum CashEntryStatus: string
{
    case Pending = 'pending';
    case Settled = 'settled';
}
