<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Cash\CashEntry;
use App\Domain\Cash\CashJournalConverter;
use App\Domain\Journal\JournalEntry;

/**
 * 収支管理サービス.
 *
 * CashEntry を JournalEntry に変換するユースケースを提供する.
 * 永続化は呼び出し側 (レガシーブリッジ) の責任とし、
 * このサービスは変換ロジックのみを担う.
 */
final class CashService
{
    /**
     * CashEntry を JournalEntry に変換する.
     *
     * 入金 → 借方:現金, 貸方:相手科目
     * 出金 → 借方:相手科目, 貸方:現金
     */
    public function toJournalEntry(CashEntry $entry): JournalEntry
    {
        return CashJournalConverter::toJournalEntry($entry);
    }

    /**
     * 複数の CashEntry を JournalEntry のリストに変換する.
     *
     * @param list<CashEntry> $entries
     * @return list<JournalEntry>
     */
    public function toJournalEntries(array $entries): array
    {
        return array_map(
            fn (CashEntry $e): JournalEntry => $this->toJournalEntry($e),
            $entries,
        );
    }
}
