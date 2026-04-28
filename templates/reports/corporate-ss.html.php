<?php
declare(strict_types=1);

use App\Domain\FinancialStatement\EquitySection;
use App\Domain\Report\HtmlHelper;

/**
 * 法人 株主資本等変動計算書
 * 会社計算規則 様式第7号相当
 *
 * @var string $companyName
 * @var \App\Domain\FiscalPeriod\FiscalPeriod $period
 * @var \App\Domain\FinancialStatement\StatementOfEquity $equity
 */
$startY = $period->startDate()->format('Y');
$startM = $period->startDate()->format('n');
$endY   = $period->endDate()->format('Y');
$endM   = $period->endDate()->format('n');
$endD   = $period->endDate()->format('j');

// string キーで管理する (enum を配列キーにすると PHP がオフセット型エラーを出す)
$sections = [
    EquitySection::CapitalStock->value     => ['enum' => EquitySection::CapitalStock,     'label' => '資本金'],
    EquitySection::CapitalSurplus->value   => ['enum' => EquitySection::CapitalSurplus,   'label' => '資本剰余金'],
    EquitySection::RetainedEarnings->value => ['enum' => EquitySection::RetainedEarnings, 'label' => '利益剰余金'],
    EquitySection::TreasuryStock->value    => ['enum' => EquitySection::TreasuryStock,    'label' => '自己株式'],
    EquitySection::Other->value            => ['enum' => EquitySection::Other,            'label' => 'その他'],
];
$colCount = count($sections) + 2;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>株主資本等変動計算書</title>
<style>
<?php readfile(__DIR__ . '/shared.css'); ?>
table.ss-table td, table.ss-table th { font-size: 8.5pt; }
</style>
</head>
<body>

<h1 class="report-title">株主資本等変動計算書</h1>

<div class="report-header">
    <div class="entity-name"><?= HtmlHelper::e($companyName) ?></div>
    <div class="period-info">
        自 <?= HtmlHelper::e($startY) ?>年<?= HtmlHelper::e($startM) ?>月1日
        至 <?= HtmlHelper::e($endY) ?>年<?= HtmlHelper::e($endM) ?>月<?= HtmlHelper::e($endD) ?>日
    </div>
</div>

<table class="report-table ss-table">
    <thead>
        <tr>
            <th class="label">区分</th>
            <?php foreach ($sections as $info): ?>
            <th><?= HtmlHelper::e($info['label']) ?></th>
            <?php endforeach; ?>
            <th>純資産合計</th>
        </tr>
    </thead>
    <tbody>
        <tr class="subtotal">
            <td class="label">当期首残高</td>
            <?php foreach ($sections as $info): ?>
            <?php $bal = $equity->openingBalance($info['enum']); ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bal->toBigDecimal()->toInt())) ?></td>
            <?php endforeach; ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($equity->totalEquityOpening()->toBigDecimal()->toInt())) ?></td>
        </tr>
        <tr class="section-header">
            <td class="label" colspan="<?= $colCount ?>">当期変動額</td>
        </tr>
        <tr>
            <td class="label indent-1">当期純利益</td>
            <?php foreach ($sections as $_): ?>
            <td class="amount">—</td>
            <?php endforeach; ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($equity->totalChange()->toBigDecimal()->toInt())) ?></td>
        </tr>
        <tr>
            <td class="label indent-1">剰余金の配当</td>
            <?php foreach ($sections as $_): ?>
            <td class="amount">—</td>
            <?php endforeach; ?>
            <td class="amount">—</td>
        </tr>
        <tr class="subtotal">
            <td class="label">当期変動額合計</td>
            <?php foreach ($sections as $_): ?>
            <td class="amount">—</td>
            <?php endforeach; ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($equity->totalChange()->toBigDecimal()->toInt())) ?></td>
        </tr>
        <tr class="total">
            <td class="label">当期末残高</td>
            <?php foreach ($sections as $info): ?>
            <?php $bal = $equity->closingBalance($info['enum']); ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($bal->toBigDecimal()->toInt())) ?></td>
            <?php endforeach; ?>
            <td class="amount"><?= HtmlHelper::e(HtmlHelper::money($equity->totalEquityClosing()->toBigDecimal()->toInt())) ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>
