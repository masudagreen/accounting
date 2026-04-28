<?php

declare(strict_types=1);

namespace App\Domain\Ledger;

use App\Domain\AccountTitle\NormalBalance;
use App\Domain\Journal\JournalEntry;
use App\Domain\Money\Money;

/**
 * 元帳: 仕訳の集まりから科目別エントリリストを構築する集計オブジェクト.
 *
 * 元実装の `accountingLogCalcJpn` テーブルの代替で、メモリ内集計版.
 * 永続化が必要な場合は別途 Repository インターフェースで実装.
 */
final class Ledger
{
    /** @var array<string, list<LedgerEntry>> */
    private array $entriesByAccount = [];

    /**
     * @param list<array{date: \DateTimeImmutable, entry: JournalEntry}> $journalEntries
     */
    public static function fromJournalEntries(array $journalEntries): self
    {
        $ledger = new self();
        foreach ($journalEntries as $row) {
            $ledger->addJournalEntry($row['date'], $row['entry']);
        }
        return $ledger;
    }

    private function addJournalEntry(\DateTimeImmutable $date, JournalEntry $entry): void
    {
        foreach ($entry->debits() as $line) {
            $this->push(new LedgerEntry(
                date: $date,
                accountTitleId: $line->accountTitleId(),
                side: NormalBalance::Debit,
                amount: $line->amount(),
                departmentId: $line->departmentId(),
                subAccountTitleId: $line->subAccountTitleId(),
            ));
        }
        foreach ($entry->credits() as $line) {
            $this->push(new LedgerEntry(
                date: $date,
                accountTitleId: $line->accountTitleId(),
                side: NormalBalance::Credit,
                amount: $line->amount(),
                departmentId: $line->departmentId(),
                subAccountTitleId: $line->subAccountTitleId(),
            ));
        }
    }

    private function push(LedgerEntry $entry): void
    {
        $this->entriesByAccount[$entry->accountTitleId()][] = $entry;
    }

    /**
     * @return list<LedgerEntry>
     */
    public function entriesFor(string $accountTitleId, ?LedgerFilter $filter = null): array
    {
        $entries = $this->entriesByAccount[$accountTitleId] ?? [];
        if ($filter === null) {
            return $entries;
        }
        return array_values(array_filter($entries, static fn (LedgerEntry $e) => $filter->matches($e)));
    }

    public function totalDebits(string $accountTitleId, ?LedgerFilter $filter = null): Money
    {
        return $this->total($accountTitleId, NormalBalance::Debit, $filter);
    }

    public function totalCredits(string $accountTitleId, ?LedgerFilter $filter = null): Money
    {
        return $this->total($accountTitleId, NormalBalance::Credit, $filter);
    }

    private function total(string $accountTitleId, NormalBalance $side, ?LedgerFilter $filter): Money
    {
        $sum = Money::zero();
        foreach ($this->entriesFor($accountTitleId, $filter) as $entry) {
            if ($entry->side() === $side) {
                $sum = $sum->plus($entry->amount());
            }
        }
        return $sum;
    }

    /**
     * 科目残高 = (通常残高方向の累計) - (反対方向の累計).
     * 借方科目で借方>貸方なら正、それ以外は負.
     */
    public function balance(
        string $accountTitleId,
        NormalBalance $normalBalance,
        ?LedgerFilter $filter = null,
    ): Money {
        $debit = $this->totalDebits($accountTitleId, $filter);
        $credit = $this->totalCredits($accountTitleId, $filter);
        return $normalBalance === NormalBalance::Debit
            ? $debit->minus($credit)
            : $credit->minus($debit);
    }
}
