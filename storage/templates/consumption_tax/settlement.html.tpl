{extends file="layout.html.tpl"}
{block name="content"}

  <h2>I. 売上区分別内訳</h2>
  <table class="section-table">
    <thead>
      <tr>
        <th style="width: 36%">税率 / 区分</th>
        <th>課税標準額 (本体)</th>
        <th>消費税額</th>
      </tr>
    </thead>
    <tbody>
      {if !$report.salesRows}
        <tr><td class="label" colspan="3">課税売上の明細はありません。</td></tr>
      {else}
        {foreach from=$report.salesRows item=row}
          <tr>
            <td class="label">{$row.label|escape}</td>
            <td class="amount">{$row.base|escape}</td>
            <td class="amount">{$row.tax|escape}</td>
          </tr>
        {/foreach}
      {/if}
    </tbody>
  </table>

  <h2>II. 仕入区分別内訳</h2>
  <table class="section-table">
    <thead>
      <tr>
        <th style="width: 36%">税率 / 区分</th>
        <th>課税仕入 (本体)</th>
        <th>控除対象消費税額</th>
      </tr>
    </thead>
    <tbody>
      {if !$report.purchaseRows}
        <tr><td class="label" colspan="3">課税仕入の明細はありません。</td></tr>
      {else}
        {foreach from=$report.purchaseRows item=row}
          <tr>
            <td class="label">{$row.label|escape}</td>
            <td class="amount">{$row.base|escape}</td>
            <td class="amount">{$row.tax|escape}</td>
          </tr>
        {/foreach}
      {/if}
    </tbody>
  </table>

  <h2>III. 売上内訳と課税売上割合</h2>
  <table class="summary-table">
    <tr><th>課税売上</th><td class="amount">{$report.taxableSales|escape}</td></tr>
    <tr><th>非課税売上</th><td class="amount">{$report.nonTaxableSales|escape}</td></tr>
    <tr><th>免税売上（輸出）</th><td class="amount">{$report.exemptSales|escape}</td></tr>
    <tr><th>不課税売上</th><td class="amount">{$report.untaxedSales|escape}</td></tr>
    <tr class="highlight"><th>売上合計 (課税+非課税+免税)</th><td class="amount">{$report.totalSales|escape}</td></tr>
    <tr class="highlight"><th>課税売上割合</th><td class="amount">{$report.taxableSalesRatio|escape}</td></tr>
  </table>

  <h2>IV. 納付税額</h2>
  <table class="summary-table">
    <tr><th>課税売上に係る消費税額（出力税）</th><td class="amount">{$report.outputTax|escape}</td></tr>
    <tr><th>控除対象仕入税額（入力税）</th><td class="amount">{$report.deductibleInputTax|escape}</td></tr>
    <tr><th>うちインボイス非登録事業者分の控除不能額</th><td class="amount">{$report.adjustmentForNonRegistered|escape}</td></tr>
    <tr class="highlight"><th>差引納付税額</th><td class="amount">{$report.netTaxPayable|escape}</td></tr>
    <tr><th>うち国税分 (7.8% / 6.24% / 6.3%)</th><td class="amount">{$report.taxSplitNational|escape}</td></tr>
    <tr><th>うち地方消費税分 (2.2% / 1.76% / 1.7%)</th><td class="amount">{$report.taxSplitLocal|escape}</td></tr>
  </table>

{/block}
