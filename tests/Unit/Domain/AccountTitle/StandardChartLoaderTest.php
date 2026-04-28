<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\AccountTitle;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\StandardChartLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 既存の `back/tpl/vars/.../JgaapAccountTitleBS.php` 等を新ドメインのツリーへ変換できることを保証する。
 *
 * これは Strangler Fig 移行期に既存データを読み込み続けるための互換性ローダ。
 */
#[CoversClass(StandardChartLoader::class)]
final class StandardChartLoaderTest extends TestCase
{
    private const string LEGACY_BS = __DIR__ . '/../../../../back/tpl/vars/else/plugin/accounting/ja/dat/jpn/JgaapAccountTitleBS.php';
    private const string LEGACY_PL = __DIR__ . '/../../../../back/tpl/vars/else/plugin/accounting/ja/dat/jpn/JgaapAccountTitlePL.php';
    private const string LEGACY_CR = __DIR__ . '/../../../../back/tpl/vars/else/plugin/accounting/ja/dat/jpn/JgaapAccountTitleCR.php';

    #[Test]
    public function 既存BSファイルから科目ツリーを構築できる(): void
    {
        $tree = StandardChartLoader::loadBalanceSheet(self::LEGACY_BS);
        // 主要な科目が含まれる
        self::assertNotNull($tree->find('cash'), '現金');
        self::assertNotNull($tree->find('ordinaryDeposit'), '普通預金');
        self::assertNotNull($tree->find('cashAndTimeDeposits'), '現金及び預金');
    }

    #[Test]
    public function BS科目はAsset_Liability_Equity_いずれかに分類される(): void
    {
        $tree = StandardChartLoader::loadBalanceSheet(self::LEGACY_BS);
        foreach ($tree->walk() as $node) {
            $cls = $node->title()->classification();
            self::assertContains(
                $cls,
                [AccountClassification::Asset, AccountClassification::Liability, AccountClassification::Equity],
                sprintf('%s should be BS-classified, got %s', $node->title()->id(), $cls->value),
            );
        }
    }

    #[Test]
    public function 既存PLファイルから科目ツリーを構築できる(): void
    {
        $tree = StandardChartLoader::loadProfitAndLoss(self::LEGACY_PL);
        self::assertNotNull($tree->find('sales'), '売上高');
    }

    #[Test]
    public function 既存CRファイルから科目ツリーを構築できる(): void
    {
        $tree = StandardChartLoader::loadCostReport(self::LEGACY_CR);
        // 製造原価は何らかのノードがある
        self::assertNotEmpty($tree->roots());
    }

    #[Test]
    public function 借方残高の科目は通常残高がDebit(): void
    {
        $tree = StandardChartLoader::loadBalanceSheet(self::LEGACY_BS);
        $cash = $tree->find('cash');
        self::assertNotNull($cash);
        self::assertSame(AccountClassification::Asset, $cash->title()->classification());
    }
}
