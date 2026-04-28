<?php
declare(strict_types=1);

use App\Domain\Report\HtmlHelper;

/**
 * 借入金及び支払利子の内訳書
 *
 * @var \App\Domain\Report\DetailedAccount\LoansPayableBreakdown $data
 */
$endY = $data->fiscalPeriod->endDate()->format('Y');
$endM = $data->fiscalPeriod->endDate()->format('n');
$endD = $data->fiscalPeriod->endDate()->format('j');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>借入金及び支払利子の内訳書</title>
<style>
<?php readfile(dirname(__DIR__) . '/shared.css'); ?>
</style>
</head>
<body>

<h1 class="report-title">借入金及び支払利子の内訳書</h1>

<div class="report-header">
    <div class="entity-name"><?= HtmlHelper::e($data->companyName) ?></div>
    <div class="period-info">
        <?= HtmlHelper::e($endY) ?>年<?= HtmlHelper::e($endM) ?>月<?= HtmlHelper::e($endD) ?>日現在
    </div>
</div>

<table class="report-table">
    <thead>
        <tr>
            <th class="label" style="width:28%">借入先名称</th>
            <th style="width:22%">所在地</th>
            <th style="width:20%">期末残高（円）</th>
            <th style="width:20%">当期支払利子（円）</th>
            <th style="width:10%">利率（%）</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->rows as $row): ?>
        <tr>
            <td class="label"><?= HtmlHelper::e($row->lenderName) ?></td>
            <td><?= HtmlHelper::e($row->location) ?></td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($row->closingBalance)) ?></td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($row->interestPaid)) ?></td>
            <td class="amount"><?= HtmlHelper::e($row->interestRate) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($data->rows === []): ?>
        <tr>
            <td class="label" colspan="5" style="text-align:center">（該当なし）</td>
        </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="total">
            <td class="label" colspan="2">合計</td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($data->totalBalance())) ?></td>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($data->totalInterestPaid())) ?></td>
            <td class="amount"></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
