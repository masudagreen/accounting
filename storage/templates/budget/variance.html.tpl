{extends file="layout.html.tpl"}

{block name="header"}
  <div class="meta">
    <span>予算名: {$analysis.budgetName|escape}</span>
    <span>EntityID: {$analysis.entityId|escape}</span>
    <span>対象期間: {$analysis.periodFrom|escape} 〜 {$analysis.periodTo|escape}</span>
    <span>状態:
      <span class="status-badge status-{$analysis.status|escape}">{$analysis.status|escape|upper}</span>
    </span>
    <span>通貨: {$analysis.currencyCode|escape}</span>
    <span>生成日時: {$analysis.generatedAt|escape}</span>
  </div>
{/block}

{block name="content"}
  <h2>予実対比 サマリー</h2>
  <table class="budget-table">
    <thead>
      <tr>
        <th>区分</th>
        <th>予算</th>
        <th>実績</th>
        <th>差異</th>
      </tr>
    </thead>
    <tbody>
      <tr class="total-row">
        <td class="label">合計</td>
        <td class="amount">{$analysis.totals.budget|escape}</td>
        <td class="amount">{$analysis.totals.actual|escape}</td>
        <td class="amount">{$analysis.totals.variance|escape}</td>
      </tr>
    </tbody>
  </table>

  <h2>科目別 予実対比</h2>
  <table class="budget-table">
    <thead>
      <tr>
        <th style="width: 10%">コード</th>
        <th style="width: 30%">勘定科目</th>
        <th>予算</th>
        <th>実績</th>
        <th>差異</th>
        <th>消化率(%)</th>
      </tr>
    </thead>
    <tbody>
      {if !$analysis.rows}
        <tr><td class="label" colspan="6">対象行がありません。</td></tr>
      {else}
        {foreach from=$analysis.rows item=r}
          <tr class="{if $r.isOverBudget}over-budget{elseif $r.isUnderBudget}under-budget{/if}">
            <td class="code">{$r.accountTitleCode|escape}</td>
            <td class="label">{$r.accountTitleName|escape}</td>
            <td class="amount">{$r.budget|escape}</td>
            <td class="amount">{$r.actual|escape}</td>
            <td class="amount">{$r.variance|escape}</td>
            <td class="usage {if $r.isOverBudget}over{elseif $r.isUnderBudget}under{/if}">{$r.usage|escape}</td>
          </tr>
        {/foreach}
      {/if}
    </tbody>
  </table>
{/block}
