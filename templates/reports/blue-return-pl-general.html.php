<?php
declare(strict_types=1);

use App\Domain\Report\HtmlHelper;

/**
 * 青色申告決算書 — 損益計算書 (一般用) + 貸借対照表
 *
 * @var string $businessName
 * @var \App\Domain\FiscalPeriod\FiscalPeriod $period
 * @var \App\Application\Dto\ProfitAndLossDto $pl
 * @var \App\Application\Dto\BalanceSheetDto $bs
 */
$startY = $period->startDate()->format('Y');
$startM = $period->startDate()->format('n');
$endY   = $period->endDate()->format('Y');
$endM   = $period->endDate()->format('n');
$endD   = $period->endDate()->format('j');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>青色申告決算書</title>
<style>
<?php readfile(__DIR__ . '/shared.css'); ?>
</style>
</head>
<body>

<h1 class="report-title">青色申告決算書（一般用）</h1>

<div class="report-header">
    <div class="entity-name"><?= HtmlHelper::e($businessName) ?></div>
    <div class="period-info">
        自 <?= HtmlHelper::e($startY) ?>年<?= HtmlHelper::e($startM) ?>月1日<br>
        至 <?= HtmlHelper::e($endY) ?>年<?= HtmlHelper::e($endM) ?>月<?= HtmlHelper::e($endD) ?>日
    </div>
</div>

<h2 class="section-title">損益計算書</h2>

<table class="report-table">
    <colgroup>
        <col style="width: 55%;">
        <col style="width: 45%;">
    </colgroup>
    <thead>
        <tr>
            <th class="label">科目</th>
            <th>金額（円）</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="label">売上（収入）金額</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->sales)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">売上原価</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">期首商品（製品）棚卸高</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">仕入金額（製品製造原価）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->costOfSales)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">期末商品（製品）棚卸高</td>
            <td class="amount">—</td>
        </tr>
        <tr class="subtotal">
            <td class="label">差引金額（売上総利益）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->grossProfit)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">経費の部</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">給料賃金</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">外注工賃</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">減価償却費</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">地代家賃</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">利子割引料</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->nonOperatingExpenses)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">租税公課</td>
            <td class="amount">—</td>
        </tr>
        <tr>
            <td class="label indent-1">その他の経費</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->sellingAndAdmin)) ?></td>
        </tr>
        <tr class="subtotal">
            <td class="label">経費合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->sellingAndAdmin + $pl->nonOperatingExpenses)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">各種引当金・準備金等</td>
            <td class="amount">—</td>
        </tr>
        <tr class="total">
            <td class="label">所得金額（差引損益）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->netIncome)) ?></td>
        </tr>
    </tbody>
</table>

<h2 class="section-title">貸借対照表</h2>

<div class="two-column">
    <div>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="label" colspan="2">資産の部</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td class="label">流動資産</td>
                    <td class="amount"></td>
                </tr>
                <tr>
                    <td class="label indent-1">現金・預金</td>
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
                <tr class="section-header">
                    <td class="label">固定資産</td>
                    <td class="amount"></td>
                </tr>
                <tr>
                    <td class="label indent-1">減価償却資産</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">土地</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="total">
                    <td class="label">資産合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalAssets)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="label" colspan="2">負債・元入金の部</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td class="label">負債</td>
                    <td class="amount"></td>
                </tr>
                <tr>
                    <td class="label indent-1">買掛金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">借入金</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">未払金</td>
                    <td class="amount">—</td>
                </tr>
                <tr class="subtotal">
                    <td class="label">負債合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalLiabilities)) ?></td>
                </tr>
                <tr class="section-header">
                    <td class="label">元入金</td>
                    <td class="amount"></td>
                </tr>
                <tr>
                    <td class="label indent-1">元入金（前年末残高）</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">事業主借</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">事業主貸（△）</td>
                    <td class="amount">—</td>
                </tr>
                <tr>
                    <td class="label indent-1">期末元入金</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalEquity)) ?></td>
                </tr>
                <tr class="total">
                    <td class="label">負債・元入金合計</td>
                    <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bs->totalAssets)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
