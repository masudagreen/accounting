<?php
declare(strict_types=1);

use App\Domain\Report\HtmlHelper;

/**
 * 法人 損益計算書
 * 会社計算規則 様式第6号相当
 *
 * @var string $companyName
 * @var \App\Domain\FiscalPeriod\FiscalPeriod $period
 * @var \App\Application\Dto\ProfitAndLossDto $pl
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
<title>損益計算書</title>
<style>
<?php readfile(__DIR__ . '/shared.css'); ?>
</style>
</head>
<body>

<h1 class="report-title">損益計算書</h1>

<div class="report-header">
    <div class="entity-name"><?= HtmlHelper::e($companyName) ?></div>
    <div class="period-info">
        自 <?= HtmlHelper::e($startY) ?>年<?= HtmlHelper::e($startM) ?>月1日<br>
        至 <?= HtmlHelper::e($endY) ?>年<?= HtmlHelper::e($endM) ?>月<?= HtmlHelper::e($endD) ?>日
    </div>
</div>

<table class="report-table">
    <colgroup>
        <col style="width: 60%;">
        <col style="width: 40%;">
    </colgroup>
    <thead>
        <tr>
            <th class="label">科目</th>
            <th>金額（円）</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="label">売上高</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->sales)) ?></td>
        </tr>
        <tr>
            <td class="label">売上原価</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->costOfSales)) ?></td>
        </tr>
        <tr class="subtotal">
            <td class="label">売上総利益（または損失）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->grossProfit)) ?></td>
        </tr>
        <tr>
            <td class="label">販売費及び一般管理費</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->sellingAndAdmin)) ?></td>
        </tr>
        <tr class="subtotal">
            <td class="label">営業利益（または損失）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->operatingIncome)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">営業外収益</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">受取利息及び配当金</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->nonOperatingIncome)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">営業外収益合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->nonOperatingIncome)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">営業外費用</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">支払利息</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->nonOperatingExpenses)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">営業外費用合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->nonOperatingExpenses)) ?></td>
        </tr>
        <tr class="subtotal">
            <td class="label">経常利益（または損失）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->ordinaryIncome)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">特別利益</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">固定資産売却益</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->extraordinaryIncome)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">特別利益合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->extraordinaryIncome)) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label">特別損失</td>
            <td class="amount"></td>
        </tr>
        <tr>
            <td class="label indent-1">固定資産売却損</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->extraordinaryLosses)) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">特別損失合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->extraordinaryLosses)) ?></td>
        </tr>
        <tr class="subtotal">
            <td class="label">税引前当期純利益（または損失）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->incomeBeforeTax)) ?></td>
        </tr>
        <tr>
            <td class="label">法人税・住民税及び事業税</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->tax)) ?></td>
        </tr>
        <tr>
            <td class="label">法人税等調整額</td>
            <td class="amount">—</td>
        </tr>
        <tr class="total">
            <td class="label">当期純利益（または損失）</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($pl->netIncome)) ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>
