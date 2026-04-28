<?php

declare(strict_types=1);

namespace App\Domain\TrialBalance;

use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\NormalBalance;
use App\Domain\Ledger\Ledger;
use App\Domain\Money\Money;

/**
 * 残高試算表 (Trial Balance).
 *
 * 不変条件:
 *  - 全科目の借方累計合計 == 全科目の貸方累計合計
 *  - 期首残高 + 当期発生 = 期末残高 (科目別, 通常残高方向で)
 */
final readonly class TrialBalance
{
    /**
     * @param array<string, TrialBalanceRow> $rowsByAccount
     */
    private function __construct(
        private array $rowsByAccount,
    ) {
    }

    public static function build(
        AccountTree $tree,
        OpeningBalances $opening,
        Ledger $ledger,
    ): self {
        $rows = [];
        foreach ($tree->walk() as $node) {
            $title = $node->title();
            $id = $title->id();

            $periodDebits = $ledger->totalDebits($id);
            $periodCredits = $ledger->totalCredits($id);
            $openingAmount = $opening->amountFor($id);

            // 期末残高 = 期首 + (通常残高方向の発生 - 反対方向の発生)
            $closing = $title->normalBalance() === NormalBalance::Debit
                ? $openingAmount->plus($periodDebits)->minus($periodCredits)
                : $openingAmount->plus($periodCredits)->minus($periodDebits);

            $rows[$id] = new TrialBalanceRow(
                accountTitle: $title,
                opening: $openingAmount,
                periodDebits: $periodDebits,
                periodCredits: $periodCredits,
                closing: $closing,
            );
        }
        return new self($rows);
    }

    /**
     * @return array<string, TrialBalanceRow>
     */
    public function rows(): array
    {
        return $this->rowsByAccount;
    }

    public function rowFor(string $accountTitleId): ?TrialBalanceRow
    {
        return $this->rowsByAccount[$accountTitleId] ?? null;
    }

    public function closingBalance(string $accountTitleId): Money
    {
        $row = $this->rowFor($accountTitleId);
        return $row?->closing() ?? Money::zero();
    }

    /**
     * 全科目の (期首が借方残高 + 当期借方発生) の合計.
     */
    public function totalDebits(): Money
    {
        $sum = Money::zero();
        foreach ($this->rowsByAccount as $row) {
            $sum = $sum->plus($row->periodDebits());
            if ($row->normalBalance() === NormalBalance::Debit) {
                $sum = $sum->plus($row->opening());
            }
        }
        return $sum;
    }

    public function totalCredits(): Money
    {
        $sum = Money::zero();
        foreach ($this->rowsByAccount as $row) {
            $sum = $sum->plus($row->periodCredits());
            if ($row->normalBalance() === NormalBalance::Credit) {
                $sum = $sum->plus($row->opening());
            }
        }
        return $sum;
    }
}
