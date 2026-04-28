<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 勘定科目ツリー全体。
 * 元実装の `JgaapAccountTitleBS.php` 等の入れ子配列に対応。
 */
final readonly class AccountTree
{
    /**
     * @param list<AccountTreeNode>     $roots
     * @param array<string, AccountTreeNode> $byId
     */
    private function __construct(
        private array $roots,
        private array $byId,
    ) {
    }

    /**
     * @param list<AccountTreeNode> $roots
     */
    public static function of(array $roots): self
    {
        $byId = [];
        foreach ($roots as $root) {
            self::indexNode($root, $byId);
        }
        return new self($roots, $byId);
    }

    /**
     * @param array<string, AccountTreeNode> $byId
     */
    private static function indexNode(AccountTreeNode $node, array &$byId): void
    {
        $id = $node->title()->id();
        if (isset($byId[$id])) {
            throw new \DomainException(sprintf('duplicate account id: %s', $id));
        }
        $byId[$id] = $node;
        foreach ($node->children() as $child) {
            self::indexNode($child, $byId);
        }
    }

    /** @return list<AccountTreeNode> */
    public function roots(): array
    {
        return $this->roots;
    }

    public function find(string $id): ?AccountTreeNode
    {
        return $this->byId[$id] ?? null;
    }

    /**
     * 全ノードを深さ優先で列挙。
     *
     * @return \Generator<AccountTreeNode>
     */
    public function walk(): \Generator
    {
        foreach ($this->roots as $root) {
            yield from self::walkNode($root);
        }
    }

    /** @return \Generator<AccountTreeNode> */
    private static function walkNode(AccountTreeNode $node): \Generator
    {
        yield $node;
        foreach ($node->children() as $child) {
            yield from self::walkNode($child);
        }
    }
}
