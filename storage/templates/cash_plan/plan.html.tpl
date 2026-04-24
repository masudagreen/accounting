{extends file="layout.html.tpl"}
{block name="content"}
  <div class="plan-summary">
    <span><span class="label">期首残高:</span> {$plan.openingBalance|escape}</span>
    {if $plan.notes}<span><span class="label">備考:</span> {$plan.notes|escape}</span>{/if}
  </div>

  <table class="plan-table">
    <thead>
      <tr>
        <th style="width: 18%">項目</th>
        {foreach from=$plan.months item=m}
          <th>{$m|escape}月</th>
        {/foreach}
        <th>合計</th>
      </tr>
    </thead>
    <tbody>
      {if !$plan.entries}
        <tr><td class="label" colspan="{14}">登録されている明細がありません。</td></tr>
      {else}
        {foreach from=$plan.entries item=e}
          <tr class="group-{$e.group|escape} {if !$e.isInflow}outflow{/if}">
            <td class="label">{$e.label|escape}</td>
            {foreach from=$e.cells item=c}
              <td class="amount">{$c|escape}</td>
            {/foreach}
            <td class="amount">{$e.total|escape}</td>
          </tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="label">月次収支</td>
        {foreach from=$plan.monthlyDeltas item=d}
          <td class="amount">{$d|escape}</td>
        {/foreach}
        <td class="amount"></td>
      </tr>
      <tr class="closing-row">
        <td class="label">月末残高</td>
        {foreach from=$plan.closingBalances item=b}
          <td class="amount">{$b|escape}</td>
        {/foreach}
        <td class="amount"></td>
      </tr>
    </tbody>
  </table>
{/block}
