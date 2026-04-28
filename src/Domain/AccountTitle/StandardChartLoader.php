<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 既存の `back/tpl/vars/else/plugin/accounting/ja/dat/jpn/Jgaap*.php` を読み込み、
 * 新ドメインの AccountTree に変換する互換性ローダ.
 *
 * 既存ファイルの形式 (ネストした PHP 配列):
 *   $vars = array(
 *     array(
 *       'strTitle' => '資産',
 *       'vars' => array('idTarget' => 'assets', 'flagDebit' => 1),
 *       'child' => array(...),
 *     ),
 *     ...
 *   );
 */
final class StandardChartLoader
{
    /**
     * BS: 第1階層の idTarget で Asset/Liability/Equity を判別する。
     * `*Sum` は集計ノード。Net 系も同様に親区分に従う。
     */
    private const array BS_ROOT_TO_CLASSIFICATION = [
        'assets'                   => AccountClassification::Asset,
        'assetsSum'                => AccountClassification::Asset,
        'liabilities'              => AccountClassification::Liability,
        'liabilitiesSum'           => AccountClassification::Liability,
        'netAssets'                => AccountClassification::Equity,
        'netAssetsSum'             => AccountClassification::Equity,
        'liabilitiesNetAssetsNet'  => AccountClassification::Equity,
    ];

    /** PL: 第1階層の idTarget で Revenue/Expense を判別する。 */
    private const array PL_ROOT_TO_CLASSIFICATION = [
        // 収益
        'sales'                                          => AccountClassification::Revenue,
        'salesSum'                                       => AccountClassification::Revenue,
        'nonOperatingIncome'                             => AccountClassification::Revenue,
        'nonOperatingIncomeSum'                          => AccountClassification::Revenue,
        'extraordinaryIncome'                            => AccountClassification::Revenue,
        'extraordinaryIncomeSum'                         => AccountClassification::Revenue,
        // 利益指標 (サマリ用ノード). 計算結果を表示するだけなので Revenue 側に分類.
        'grossProfitOrLossNet'                           => AccountClassification::Revenue,
        'operatingIncomeProfitOrLossNet'                 => AccountClassification::Revenue,
        'ordinaryProfitNet'                              => AccountClassification::Revenue,
        'currentTermProfitOrLossPreNet'                  => AccountClassification::Revenue,
        'currentTermProfitOrLossNet'                     => AccountClassification::Revenue,
        // 費用
        'costOfSales'                                    => AccountClassification::Expense,
        'costOfSalesSum'                                 => AccountClassification::Expense,
        'sellingGeneralAndAdministrationExpenses'        => AccountClassification::Expense,
        'sellingGeneralAndAdministrationExpensesSum'     => AccountClassification::Expense,
        'nonOperatingExpenses'                           => AccountClassification::Expense,
        'nonOperatingExpensesSum'                        => AccountClassification::Expense,
        'extraordinaryLosses'                            => AccountClassification::Expense,
        'extraordinaryLossesSum'                         => AccountClassification::Expense,
        'corporateInhabitantAndEnterpriseTax'            => AccountClassification::Expense,
        'corporateTaxAdjustments'                        => AccountClassification::Expense,
    ];

    /** PL: root id → PlSection. 計算結果ノード (xxxNet) はマップに含めない. */
    private const array PL_ROOT_TO_SECTION = [
        'sales'                                          => PlSection::Sales,
        'salesSum'                                       => PlSection::Sales,
        'costOfSales'                                    => PlSection::CostOfSales,
        'costOfSalesSum'                                 => PlSection::CostOfSales,
        'sellingGeneralAndAdministrationExpenses'        => PlSection::SellingAndAdmin,
        'sellingGeneralAndAdministrationExpensesSum'     => PlSection::SellingAndAdmin,
        'nonOperatingIncome'                             => PlSection::NonOperatingIncome,
        'nonOperatingIncomeSum'                          => PlSection::NonOperatingIncome,
        'nonOperatingExpenses'                           => PlSection::NonOperatingExpenses,
        'nonOperatingExpensesSum'                        => PlSection::NonOperatingExpenses,
        'extraordinaryIncome'                            => PlSection::ExtraordinaryIncome,
        'extraordinaryIncomeSum'                         => PlSection::ExtraordinaryIncome,
        'extraordinaryLosses'                            => PlSection::ExtraordinaryLosses,
        'extraordinaryLossesSum'                         => PlSection::ExtraordinaryLosses,
        'corporateInhabitantAndEnterpriseTax'            => PlSection::Tax,
        'corporateTaxAdjustments'                        => PlSection::Tax,
    ];

    public static function loadBalanceSheet(string $path): AccountTree
    {
        return self::load($path, self::BS_ROOT_TO_CLASSIFICATION, []);
    }

    public static function loadProfitAndLoss(string $path): AccountTree
    {
        return self::load($path, self::PL_ROOT_TO_CLASSIFICATION, self::PL_ROOT_TO_SECTION);
    }

    /** CR: root id → CrSection. */
    private const array CR_ROOT_TO_SECTION = [
        'materialsCost'                       => CrSection::Materials,
        'materialsCostSum'                    => CrSection::Materials,
        'laborCost'                           => CrSection::Labor,
        'laborCostSum'                        => CrSection::Labor,
        'manufactureCost'                     => CrSection::Manufacture,
        'manufactureCostSum'                  => CrSection::Manufacture,
        'workInProcessOpeningInventoryWrap'   => CrSection::OpeningWorkInProcess,
        'workInProcessOpeningInventoryWrapSum' => CrSection::OpeningWorkInProcess,
        'workInProcessClosingInventoryWrap'   => CrSection::ClosingWorkInProcess,
        'workInProcessClosingInventoryWrapSum' => CrSection::ClosingWorkInProcess,
        'workInProcessRemoveWrap'             => CrSection::RemoveTransfer,
        'workInProcessRemoveWrapSum'          => CrSection::RemoveTransfer,
    ];

    public static function loadCostReport(string $path): AccountTree
    {
        // 製造原価報告書は全ノードが ManufacturingCost. PlSection は付与しない.
        return self::loadWithCrSection($path, AccountClassification::ManufacturingCost);
    }

    private static function loadWithCrSection(
        string $path,
        AccountClassification $defaultClassification,
    ): AccountTree {
        if (! is_file($path)) {
            throw new \RuntimeException(sprintf('chart file not found: %s', $path));
        }

        $vars = self::includeVars($path);

        $roots = [];
        foreach ($vars as $rootData) {
            if (! is_array($rootData)) {
                continue;
            }
            $rootId = self::extractId($rootData);
            $section = self::CR_ROOT_TO_SECTION[$rootId] ?? null;
            $roots[] = self::convertNodeWithCrSection($rootData, $defaultClassification, $section);
        }

        return AccountTree::of($roots);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function convertNodeWithCrSection(
        array $data,
        AccountClassification $classification,
        ?CrSection $crSection = null,
    ): AccountTreeNode {
        $id = self::extractId($data);
        $title = is_string($data['strTitle'] ?? null) ? $data['strTitle'] : $id;

        $varsArr = is_array($data['vars'] ?? null) ? $data['vars'] : [];
        $fsId = is_string($varsArr['idAccountTitleJgaapFS'] ?? null)
            ? $varsArr['idAccountTitleJgaapFS']
            : null;

        $accountTitle = AccountTitle::of(
            id: $id,
            title: $title,
            classification: $classification,
            financialStatementItemId: $fsId,
            crSection: $crSection,
        );

        $children = [];
        if (is_array($data['child'] ?? null)) {
            foreach ($data['child'] as $childData) {
                if (! is_array($childData)) {
                    continue;
                }
                $children[] = self::convertNodeWithCrSection($childData, $classification, $crSection);
            }
        }

        return $children === []
            ? AccountTreeNode::leaf($accountTitle)
            : AccountTreeNode::branch($accountTitle, $children);
    }

    /**
     * @param array<string, AccountClassification> $rootToClassification
     * @param array<string, PlSection>             $rootToSection
     */
    private static function load(
        string $path,
        array $rootToClassification,
        array $rootToSection,
        ?AccountClassification $defaultClassification = null,
    ): AccountTree {
        if (! is_file($path)) {
            throw new \RuntimeException(sprintf('chart file not found: %s', $path));
        }

        $vars = self::includeVars($path);

        $roots = [];
        foreach ($vars as $rootData) {
            if (! is_array($rootData)) {
                continue;
            }
            $rootId = self::extractId($rootData);
            $cls = $rootToClassification[$rootId] ?? $defaultClassification;
            if ($cls === null) {
                throw new \DomainException(sprintf('unknown root account id: %s', $rootId));
            }
            $section = $rootToSection[$rootId] ?? null;
            $roots[] = self::convertNode($rootData, $cls, $section);
        }

        return AccountTree::of($roots);
    }

    /**
     * 既存ファイルを include して `$vars` を取得する.
     *
     * @return list<array<string, mixed>>
     */
    private static function includeVars(string $path): array
    {
        // 既存ファイルが `$vars = array(...);` を定義する前提.
        $vars = null;
        require $path;
        /** @phpstan-ignore function.impossibleType */
        if (! is_array($vars)) {
            throw new \RuntimeException(sprintf('chart file did not produce $vars array: %s', $path));
        }
        return array_values($vars);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function convertNode(
        array $data,
        AccountClassification $classification,
        ?PlSection $plSection = null,
    ): AccountTreeNode {
        $id = self::extractId($data);
        $title = is_string($data['strTitle'] ?? null) ? $data['strTitle'] : $id;

        $varsArr = is_array($data['vars'] ?? null) ? $data['vars'] : [];
        $fsId = is_string($varsArr['idAccountTitleJgaapFS'] ?? null)
            ? $varsArr['idAccountTitleJgaapFS']
            : null;

        $accountTitle = AccountTitle::of(
            id: $id,
            title: $title,
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
                $children[] = self::convertNode($childData, $classification, $plSection);
            }
        }

        return $children === []
            ? AccountTreeNode::leaf($accountTitle)
            : AccountTreeNode::branch($accountTitle, $children);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractId(array $data): string
    {
        $vars = is_array($data['vars'] ?? null) ? $data['vars'] : [];
        $id = $vars['idTarget'] ?? null;
        if (! is_string($id) || $id === '') {
            throw new \DomainException(sprintf(
                'node missing idTarget: %s',
                json_encode($data['strTitle'] ?? '?', JSON_UNESCAPED_UNICODE),
            ));
        }
        return $id;
    }
}
