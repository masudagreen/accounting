<?php

declare(strict_types=1);

namespace App\Domain\Cash;

use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;

/**
 * CashEntry を JournalEntry に変換する.
 *
 * 変換ルール:
 *  - 入金 (In)  → 借方: cashAccountTitleId / 貸方: counterAccountTitleId
 *  - 出金 (Out) → 借方: counterAccountTitleId / 貸方: cashAccountTitleId
 *
 * 金額は同額 (貸借一致).
 */
final class CashJournalConverter
{
    public static function toJournalEntry(CashEntry $entry): JournalEntry
    {
        [$debitAccountId, $creditAccountId] = match ($entry->direction()) {
            CashDirection::In  => [$entry->cashAccountTitleId(), $entry->counterAccountTitleId()],
            CashDirection::Out => [$entry->counterAccountTitleId(), $entry->cashAccountTitleId()],
        };

        return JournalEntry::of(
            debits: [JournalLine::of($debitAccountId, $entry->amount())],
            credits: [JournalLine::of($creditAccountId, $entry->amount())],
        );
    }
}
