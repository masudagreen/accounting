<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 勘定科目ツリーの1ノード。
 * 葉 (leaf) は記帳可能な科目、枝 (branch) はカテゴリのみ。
 */
final readonly class AccountTreeNode
{
    /**
     * @param list<self> $children
     */
    private function __construct(
        private AccountTitle $title,
        private array $children,
    ) {
    }

    public static function leaf(AccountTitle $title): self
    {
        return new self($title, []);
    }

    /**
     * @param list<self> $children
     */
    public static function branch(AccountTitle $title, array $children): self
    {
        return new self($title, $children);
    }

    public function title(): AccountTitle
    {
        return $this->title;
    }

    /** @return list<self> */
    public function children(): array
    {
        return $this->children;
    }

    public function isLeaf(): bool
    {
        return $this->children === [];
    }
}
