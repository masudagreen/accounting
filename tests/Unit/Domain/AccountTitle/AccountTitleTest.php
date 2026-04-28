<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\AccountTitle;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\NormalBalance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 勘定科目 (AccountTitle) の値オブジェクト。
 *
 * 元実装の `idTarget` (= 科目ID) を `id` として、`strTitle` を `title` に、
 * `flagDebit` を `NormalBalance` enum (Debit/Credit) に対応させる。
 *
 * 不変条件:
 *  - id 非空文字
 *  - 区分と通常残高方向は整合 (資産・費用→Debit / 負債・純資産・収益→Credit)
 */
#[CoversClass(AccountTitle::class)]
final class AccountTitleTest extends TestCase
{
    #[Test]
    public function 資産の通常残高は借方(): void
    {
        $title = AccountTitle::of(
            id: 'cash',
            title: '現金',
            classification: AccountClassification::Asset,
        );
        self::assertSame(NormalBalance::Debit, $title->normalBalance());
    }

    #[Test]
    public function 費用の通常残高は借方(): void
    {
        $title = AccountTitle::of('rentExpense', '地代家賃', AccountClassification::Expense);
        self::assertSame(NormalBalance::Debit, $title->normalBalance());
    }

    #[Test]
    public function 負債の通常残高は貸方(): void
    {
        $title = AccountTitle::of('accountsPayable', '買掛金', AccountClassification::Liability);
        self::assertSame(NormalBalance::Credit, $title->normalBalance());
    }

    #[Test]
    public function 純資産の通常残高は貸方(): void
    {
        $title = AccountTitle::of('capitalStock', '資本金', AccountClassification::Equity);
        self::assertSame(NormalBalance::Credit, $title->normalBalance());
    }

    #[Test]
    public function 収益の通常残高は貸方(): void
    {
        $title = AccountTitle::of('sales', '売上高', AccountClassification::Revenue);
        self::assertSame(NormalBalance::Credit, $title->normalBalance());
    }

    #[Test]
    public function ID空文字はエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AccountTitle::of('', '現金', AccountClassification::Asset);
    }

    #[Test]
    public function 表示名空文字はエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AccountTitle::of('cash', '', AccountClassification::Asset);
    }

    #[Test]
    public function FS科目ID_部門ID_補助科目可能フラグを伴える(): void
    {
        $title = AccountTitle::of(
            id: 'cash',
            title: '現金',
            classification: AccountClassification::Asset,
            financialStatementItemId: 'cash',
            allowSubAccount: true,
        );
        self::assertSame('cash', $title->financialStatementItemId());
        self::assertTrue($title->allowsSubAccount());
    }

    #[Test]
    public function 区分の集計判定_BS_PL_CR(): void
    {
        self::assertTrue(AccountClassification::Asset->isBalanceSheet());
        self::assertTrue(AccountClassification::Liability->isBalanceSheet());
        self::assertTrue(AccountClassification::Equity->isBalanceSheet());
        self::assertFalse(AccountClassification::Revenue->isBalanceSheet());

        self::assertTrue(AccountClassification::Revenue->isProfitAndLoss());
        self::assertTrue(AccountClassification::Expense->isProfitAndLoss());
        self::assertFalse(AccountClassification::Asset->isProfitAndLoss());

        self::assertTrue(AccountClassification::ManufacturingCost->isCostReport());
        self::assertFalse(AccountClassification::Asset->isCostReport());
    }
}
