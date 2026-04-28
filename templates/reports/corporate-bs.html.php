<?php
declare(strict_types=1);

use App\Domain\Report\HtmlHelper;

/**
 * 法人 貸借対照表
 * 会社計算規則 様式第5号相当
 *
 * @var string $companyName
 * @var \App\Domain\FiscalPeriod\FiscalPeriod $period
 * @var \App\Domain\Report\CorporateFinancialStatements\CorporateBsData $bs
 */
$endY = $period->endDate()->format('Y');
$endM = $period->endDate()->format('n');
$endD = $period->endDate()->format('j');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>貸借対照表</title>
<style>
<?php readfile(__DIR__ . '/shared.css'); ?>
</style>
</head>
<body>

<h1 class="report-title">貸借対照表</h1>

<div class="report-header">
    <div class="entity-name"><?= HtmlHelper::e($companyName) ?></div>
    <div class="period-info">
        <?= HtmlHelper::e($endY) ?>年<?= HtmlHelper::e($endM) ?>月<?= HtmlHelper::e($endD) ?>日現在
    </div>
</div>

<div class="two-column">
    <!-- 資産の部 -->
    <div>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="label" colspan="2">資産の部</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td class="label" colspan="2">流動資産</td>
                </tr>
                <tr>
                    <td class="label indent-1">現金及び預金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">受取手形</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">売掛金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">棚卸資産</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">前払費用</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">その他</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="subtotal">
                    <td class="label">流動資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->currentAssets)) ?></td>
                </tr>
                <tr class="section-header">
                    <td class="label" colspan="2">固定資産</td>
                </tr>
                <tr>
                    <td class="label indent-1">有形固定資産</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">無形固定資産</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">投資その他の資産</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="subtotal">
                    <td class="label">固定資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->fixedAssets)) ?></td>
                </tr>
                <tr class="total">
                    <td class="label">資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalAssets)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 負債・純資産の部 -->
    <div>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="label" colspan="2">負債の部</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td class="label" colspan="2">流動負債</td>
                </tr>
                <tr>
                    <td class="label indent-1">支払手形</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">買掛金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">短期借入金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">未払費用</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">未払法人税等</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">その他</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="subtotal">
                    <td class="label">流動負債合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->currentLiabilities)) ?></td>
                </tr>
                <tr class="section-header">
                    <td class="label" colspan="2">固定負債</td>
                </tr>
                <tr>
                    <td class="label indent-1">長期借入金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">退職給付引当金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">その他</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="subtotal">
                    <td class="label">固定負債合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->fixedLiabilities)) ?></td>
                </tr>
                <tr class="total">
                    <td class="label">負債合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalLiabilities)) ?></td>
                </tr>
            </tbody>
        </table>

        <table class="report-table">
            <thead>
                <tr>
                    <th class="label" colspan="2">純資産の部</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td class="label" colspan="2">株主資本</td>
                </tr>
                <tr>
                    <td class="label indent-1">資本金</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->capitalStock)) ?></td>
                </tr>
                <tr>
                    <td class="label indent-1">資本剰余金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">利益剰余金</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->retainedEarnings)) ?></td>
                </tr>
                <tr>
                    <td class="label indent-1">自己株式（△）</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="total">
                    <td class="label">純資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalEquity)) ?></td>
                </tr>
                <tr class="total">
                    <td class="label">負債純資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalAssets)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
