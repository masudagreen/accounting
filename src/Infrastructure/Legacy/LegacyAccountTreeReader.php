<?php

declare(strict_types=1);

namespace App\Infrastructure\Legacy;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\CrSection;
use App\Domain\AccountTitle\NormalBalance;
use App\Domain\AccountTitle\PlSection;
use App\Domain\AccountTitle\StandardChartLoader;

/**
 * accountingFSJpn テーブルの jsonJgaapAccountTitleBS / jsonJgaapAccountTitlePL / jsonJgaapAccountTitleCR
 * (JSON 配列) を読んで AccountTree を構築するリーダー.
 *
 * StandardChartLoader::load() が PHP ファイルをインクルードする形式に対して、
 * 本クラスは DB に保存された JSON (同一フォーマット) をデシリアライズして使う.
 *
 * JSON 形式は StandardChartLoader が扱う PHP 配列と同一の shape:
 *   [
 *     { "strTitle": "売上高", "vars": { "idTarget": "sales", "flagDebit": 0 }, "child": [...] },
 *     ...
 *   ]
 */
final class LegacyAccountTreeReader
{
    /**
     * BS: root idTarget → AccountClassification のマッピング
     * StandardChartLoader の定数を流用.
     */
    private const array BS_ROOT_TO_CLASSIFICATION = [
        'assets'                  => AccountClassification::Asset,
        'assetsSum'               => AccountClassification::Asset,
        'liabilities'             => AccountClassification::Liability,
        'liabilitiesSum'          => AccountClassification::Liability,
        'netAssets'               => AccountClassification::Equity,
        'netAssetsSum'            => AccountClassification::Equity,
        'liabilitiesNetAssetsNet' => AccountClassification::Equity,
    ];

    private const array PL_ROOT_TO_CLASSIFICATION = [
        'sales'                                      => AccountClassification::Revenue,
        'salesSum'                                   => AccountClassification::Revenue,
        'nonOperatingIncome'                         => AccountClassification::Revenue,
        'nonOperatingIncomeSum'                      => AccountClassification::Revenue,
        'extraordinaryIncome'                        => AccountClassification::Revenue,
        'extraordinaryIncomeSum'                     => AccountClassification::Revenue,
        'grossProfitOrLossNet'                       => AccountClassification::Revenue,
        'operatingIncomeProfitOrLossNet'             => AccountClassification::Revenue,
        'ordinaryProfitNet'                          => AccountClassification::Revenue,
        'currentTermProfitOrLossPreNet'              => AccountClassification::Revenue,
        'currentTermProfitOrLossNet'                 => AccountClassification::Revenue,
        'costOfSales'                                => AccountClassification::Expense,
        'costOfSalesSum'                             => AccountClassification::Expense,
        'sellingGeneralAndAdministrationExpenses'    => AccountClassification::Expense,
        'sellingGeneralAndAdministrationExpensesSum' => AccountClassification::Expense,
        'nonOperatingExpenses'                       => AccountClassification::Expense,
        'nonOperatingExpensesSum'                    => AccountClassification::Expense,
        'extraordinaryLosses'                        => AccountClassification::Expense,
        'extraordinaryLossesSum'                     => AccountClassification::Expense,
        'corporateInhabitantAndEnterpriseTax'        => AccountClassification::Expense,
        'corporateTaxAdjustments'                    => AccountClassification::Expense,
    ];

    private const array PL_ROOT_TO_SECTION = [
        'sales'                                      => PlSection::Sales,
        'salesSum'                                   => PlSection::Sales,
        'costOfSales'                                => PlSection::CostOfSales,
        'costOfSalesSum'                             => PlSection::CostOfSales,
        'sellingGeneralAndAdministrationExpenses'    => PlSection::SellingAndAdmin,
        'sellingGeneralAndAdministrationExpensesSum' => PlSection::SellingAndAdmin,
        'nonOperatingIncome'                         => PlSection::NonOperatingIncome,
        'nonOperatingIncomeSum'                      => PlSection::NonOperatingIncome,
        'nonOperatingExpenses'                       => PlSection::NonOperatingExpenses,
        'nonOperatingExpensesSum'                    => PlSection::NonOperatingExpenses,
        'extraordinaryIncome'                        => PlSection::ExtraordinaryIncome,
        'extraordinaryIncomeSum'                     => PlSection::ExtraordinaryIncome,
        'extraordinaryLosses'                        => PlSection::ExtraordinaryLosses,
        'extraordinaryLossesSum'                     => PlSection::ExtraordinaryLosses,
        'corporateInhabitantAndEnterpriseTax'        => PlSection::Tax,
        'corporateTaxAdjustments'                    => PlSection::Tax,
    ];

    /**
     * JSON 文字列 (accountingFSJpn.jsonJgaapAccountTitleBS) から BS AccountTree を構築する.
     */
    public function buildBalanceSheetFromJson(string $json): AccountTree
    {
        $nodes = $this->parseJson($json);
        return $this->buildTree($nodes, self::BS_ROOT_TO_CLASSIFICATION, []);
    }

    /**
     * JSON 文字列 (accountingFSJpn.jsonJgaapAccountTitlePL) から PL AccountTree を構築する.
     */
    public function buildProfitAndLossFromJson(string $json): AccountTree
    {
        $nodes = $this->parseJson($json);
        return $this->buildTree($nodes, self::PL_ROOT_TO_CLASSIFICATION, self::PL_ROOT_TO_SECTION);
    }

    /**
     * BS + PL を合体させた全科目ツリーを構築する.
     *
     * TrialBalance の不変条件検証では、仕訳に登場するすべての科目
     * (BS科目 + PL科目) がツリーに含まれる必要がある.
     *
     * 重複 ID がある場合は BS 側を優先する.
     */
    public function buildCombinedTreeFromJson(string $bsJson, string $plJson): AccountTree
    {
        $bsNodes = $this->parseJson($bsJson);
        $plNodes = $this->parseJson($plJson);

        $bsTree = $this->buildTree($bsNodes, self::BS_ROOT_TO_CLASSIFICATION, []);
        $plTree = $this->buildTree($plNodes, self::PL_ROOT_TO_CLASSIFICATION, self::PL_ROOT_TO_SECTION);

        // 重複を排除してルートノードを結合する
        $seenIds = [];
        foreach ($bsTree->walk() as $node) {
            $seenIds[$node->title()->id()] = true;
        }

        $combinedRoots = $bsTree->roots();
        foreach ($plTree->roots() as $root) {
            if (! isset($seenIds[$root->title()->id()])) {
                $combinedRoots[] = $root;
            }
        }

        return AccountTree::of($combinedRoots);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseJson(string $json): array
    {
        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON for account tree');
        }
        return array_values($decoded);
    }

    /**
     * @param list<array<string, mixed>>           $rootNodes
     * @param array<string, AccountClassification> $rootToClassification
     * @param array<string, PlSection>             $rootToSection
     */
    private function buildTree(
        array $rootNodes,
        array $rootToClassification,
        array $rootToSection,
    ): AccountTree {
        $roots = [];
        foreach ($rootNodes as $nodeData) {
            if (! is_array($nodeData)) {
                continue;
            }
            $rootId = $this->extractId($nodeData);
            if ($rootId === null) {
                continue;
            }
            $cls = $rootToClassification[$rootId] ?? null;
            if ($cls === null) {
                // unknown root: skip (summary node 等)
                continue;
            }
            $section = $rootToSection[$rootId] ?? null;
            $roots[] = $this->convertNode($nodeData, $cls, $section);
        }

        return AccountTree::of($roots);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function convertNode(
        array $data,
        AccountClassification $classification,
        ?PlSection $plSection = null,
    ): AccountTreeNode {
        $id = $this->extractId($data) ?? 'unknown_' . uniqid();
        $title = is_string($data['strTitle'] ?? null) ? (string) $data['strTitle'] : $id;

        $varsArr = is_array($data['vars'] ?? null) ? $data['vars'] : [];
        $fsId = is_string($varsArr['idAccountTitleJgaapFS'] ?? null)
            ? $varsArr['idAccountTitleJgaapFS']
            : null;

        $accountTitle = AccountTitle::of(
            id: $id,
            title: $title !== '' ? $title : $id,
            classification: $classification,
            financialStatementItemId: $fsId,
            plSection: $plSection,
        );

        $children = [];
        if (is_array($data['child'] ?? null)) {
            foreach ($data['child'] as $childData) {
                if (! is_array($childData)) {
                    continue;
                }
                $children[] = $this->convertNode($childData, $classification, $plSection);
            }
        }

        return $children === []
            ? AccountTreeNode::leaf($accountTitle)
            : AccountTreeNode::branch($accountTitle, $children);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractId(array $data): ?string
    {
        $vars = is_array($data['vars'] ?? null) ? $data['vars'] : [];
        $id = $vars['idTarget'] ?? null;
        if (! is_string($id) || $id === '') {
            return null;
        }
        return $id;
    }
}
