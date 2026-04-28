<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\AccountTitle;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 勘定科目ツリー。
 *
 * 元実装の `JgaapAccountTitleBS.php` 等は深いネストの配列で `child` キーで子を持つ。
 * 新ドメインでは AccountTree (rootと検索)、AccountTreeNode (個々のノード) で扱う。
 *
 * 不変条件:
 *  - id はツリー全体で一意
 *  - 子ノードの区分は親と一致するか親の細分類
 */
#[CoversClass(AccountTree::class)]
#[CoversClass(AccountTreeNode::class)]
final class AccountTreeTest extends TestCase
{
    #[Test]
    public function 葉ノードのみのツリー(): void
    {
        $cash = AccountTitle::of('cash', '現金', AccountClassification::Asset);
        $node = AccountTreeNode::leaf($cash);

        self::assertSame('cash', $node->title()->id());
        self::assertCount(0, $node->children());
        self::assertTrue($node->isLeaf());
    }

    #[Test]
    public function 親と子のツリー(): void
    {
        $cash = AccountTitle::of('cash', '現金', AccountClassification::Asset);
        $petty = AccountTitle::of('prettyCash', '小口現金', AccountClassification::Asset);

        $cashAndDeposits = AccountTitle::of('cashAndTimeDeposits', '現金及び預金', AccountClassification::Asset);
        $node = AccountTreeNode::branch($cashAndDeposits, [
            AccountTreeNode::leaf($cash),
            AccountTreeNode::leaf($petty),
        ]);

        self::assertCount(2, $node->children());
        self::assertFalse($node->isLeaf());
    }

    #[Test]
    public function ツリー全体から_id_検索(): void
    {
        $tree = self::sampleTree();

        $found = $tree->find('cash');
        self::assertNotNull($found);
        self::assertSame('現金', $found->title()->title());

        self::assertNull($tree->find('does-not-exist'));
    }

    #[Test]
    public function 全ノード列挙(): void
    {
        $tree = self::sampleTree();
        $ids = array_map(
            static fn ($node) => $node->title()->id(),
            iterator_to_array($tree->walk(), false),
        );
        self::assertContains('assets', $ids);
        self::assertContains('cashAndTimeDeposits', $ids);
        self::assertContains('cash', $ids);
        self::assertContains('prettyCash', $ids);
    }

    #[Test]
    public function ID重複は登録時に検出する(): void
    {
        $a1 = AccountTitle::of('cash', '現金', AccountClassification::Asset);
        $a2 = AccountTitle::of('cash', '現金B', AccountClassification::Asset);

        $this->expectException(\DomainException::class);
        AccountTree::of([
            AccountTreeNode::leaf($a1),
            AccountTreeNode::leaf($a2),
        ]);
    }

    private static function sampleTree(): AccountTree
    {
        $assets = AccountTitle::of('assets', '資産', AccountClassification::Asset);
        $cashAndDeposits = AccountTitle::of('cashAndTimeDeposits', '現金及び預金', AccountClassification::Asset);
        $cash = AccountTitle::of('cash', '現金', AccountClassification::Asset);
        $petty = AccountTitle::of('prettyCash', '小口現金', AccountClassification::Asset);

        return AccountTree::of([
            AccountTreeNode::branch($assets, [
                AccountTreeNode::branch($cashAndDeposits, [
                    AccountTreeNode::leaf($cash),
                    AccountTreeNode::leaf($petty),
                ]),
            ]),
        ]);
    }
}
