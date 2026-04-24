{extends file="layout.html.tpl"}
{block name="content"}
  <div class="status {if $analysis.isBelowBreakEven}below{else}above{/if}">
    {if $analysis.isBelowBreakEven}
      現在の売上は損益分岐点を下回っています (BEP 売上 {$analysis.bepSales|escape})。
    {else}
      現在の売上は損益分岐点を上回っています (安全余裕率 {$analysis.safetyMarginRatio|escape})。
    {/if}
  </div>

  <h2>サマリ</h2>
  <table class="summary-grid">
    <tr><th>売上高</th><td>{$analysis.sales|escape}</td></tr>
    <tr><th>変動費</th><td>{$analysis.variableCosts|escape}</td></tr>
    <tr class="highlight"><th>限界利益</th><td>{$analysis.contributionMargin|escape}</td></tr>
    <tr><th>限界利益率</th><td>{$analysis.contributionMarginRate|escape}</td></tr>
    <tr><th>固定費</th><td>{$analysis.fixedCosts|escape}</td></tr>
    <tr class="highlight"><th>損益分岐点 売上高</th><td>{$analysis.bepSales|escape}</td></tr>
    <tr><th>損益分岐点比率</th><td>{$analysis.bepRatio|escape}</td></tr>
    <tr><th>安全余裕率</th><td>{$analysis.safetyMarginRatio|escape}</td></tr>
    <tr class="highlight"><th>営業利益</th><td>{$analysis.operatingProfit|escape}</td></tr>
  </table>

  <h2>売上 内訳</h2>
  {if !$analysis.salesBreakdown}
    <p class="note">売上に該当する勘定科目がありません。</p>
  {else}
    <table class="breakdown-table">
      <thead>
        <tr><th>科目コード</th><th>科目名</th><th>金額</th></tr>
      </thead>
      <tbody>
        {foreach from=$analysis.salesBreakdown item=r}
          <tr>
            <td class="code">{$r.code|escape}</td>
            <td>{$r.name|escape}</td>
            <td class="amount">{$r.amount|escape}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}

  <h2>変動費 内訳</h2>
  {if !$analysis.variableBreakdown}
    <p class="note">変動費に該当する勘定科目がありません。</p>
  {else}
    <table class="breakdown-table">
      <thead>
        <tr><th>科目コード</th><th>科目名</th><th>分類</th><th>金額</th></tr>
      </thead>
      <tbody>
        {foreach from=$analysis.variableBreakdown item=r}
          <tr>
            <td class="code">{$r.code|escape}</td>
            <td>{$r.name|escape}</td>
            <td class="code">{$r.costType|escape}</td>
            <td class="amount">{$r.amount|escape}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}

  <h2>固定費 内訳</h2>
  {if !$analysis.fixedBreakdown}
    <p class="note">固定費に該当する勘定科目がありません。</p>
  {else}
    <table class="breakdown-table">
      <thead>
        <tr><th>科目コード</th><th>科目名</th><th>分類</th><th>金額</th></tr>
      </thead>
      <tbody>
        {foreach from=$analysis.fixedBreakdown item=r}
          <tr>
            <td class="code">{$r.code|escape}</td>
            <td>{$r.name|escape}</td>
            <td class="code">{$r.costType|escape}</td>
            <td class="amount">{$r.amount|escape}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}
{/block}
