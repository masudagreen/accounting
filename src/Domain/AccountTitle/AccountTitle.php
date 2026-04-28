<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 勘定科目 (AccountTitle).
 *
 * 元実装の `idTarget` を `id`、`strTitle` を `title`、`flagDebit` を区分から導出。
 *
 * 不変条件:
 *  - id 非空文字
 *  - 表示名 非空文字
 *  - 通常残高方向は区分から自動導出
 */
final readonly class AccountTitle
{
    private function __construct(
        private string $id,
        private string $title,
        private AccountClassification $classification,
        private ?string $financialStatementItemId,
        private bool $allowSubAccount,
    ) {
    }

    public static function of(
        string $id,
        string $title,
        AccountClassification $classification,
        ?string $financialStatementItemId = null,
        bool $allowSubAccount = false,
    ): self {
        if ($id === '') {
            throw new \InvalidArgumentException('id must not be empty');
        }
        if ($title === '') {
            throw new \InvalidArgumentException('title must not be empty');
        }
        return new self($id, $title, $classification, $financialStatementItemId, $allowSubAccount);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function classification(): AccountClassification
    {
        return $this->classification;
    }

    public function normalBalance(): NormalBalance
    {
        return $this->classification->normalBalance();
    }

    public function financialStatementItemId(): ?string
    {
        return $this->financialStatementItemId;
    }

    public function allowsSubAccount(): bool
    {
        return $this->allowSubAccount;
    }
}
