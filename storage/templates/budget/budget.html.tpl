{extends file="layout.html.tpl"}

{block name="header"}
  <div class="meta">
    <span>予算名: {$budget.name|escape}</span>
    <span>EntityID: {$budget.entityId|escape}</span>
    <span>FiscalTermID: {$budget.fiscalTermId|escape}</span>
    <span>状態:
      <span class="status-badge status-{$budget.status|escape}">{$budget.status|escape|upper}</span>
    </span>
    <span>生成日時: {$budget.generatedAt|escape}</span>
  </div>
  {if $budget.notes}
    <div class="budget-summary"><span><span class="label">備考:</span> {$budget.notes|escape}</span></div>
  {/if}
{/block}

{block name="content"}
  <table class="budget-table">
    <thead>
      <tr>
        <th style="width: 22%">勘定科目ID</th>
        {foreach from=$budget.months item=m}
          <th>{$m|escape}月</th>
        {/foreach}
        <th>年間合計</th>
      </tr>
    </thead>
    <tbody>
      {if !$budget.items}
        <tr><td class="label" colspan="14">登録されている明細がありません。</td></tr>
      {else}
        {foreach from=$budget.items item=i}
          <tr>
            <td class="code">{$i.accountTitleId|escape}{if $i.subAccountTitleId} / {$i.subAccountTitleId|escape}{/if}</td>
            {foreach from=$i.cells item=c}
              <td class="amount">{$c|escape}</td>
            {/foreach}
            <td class="amount">{$i.total|escape}</td>
          </tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="label">月次合計</td>
        {foreach from=$budget.monthlyTotals item=t}
          <td class="amount">{$t|escape}</td>
        {/foreach}
        <td class="amount">{$budget.annualTotal|escape}</td>
      </tr>
    </tbody>
  </table>
{/block}
