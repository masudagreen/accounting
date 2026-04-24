<div class="page">
  <h1>青色申告決算書 (2/4) 月別売上・仕入・給料賃金</h1>

  <h2>月別集計</h2>
  <table class="br-table">
    <thead>
      <tr>
        <th class="month">月</th>
        <th>売上金額</th>
        <th>仕入金額</th>
        <th>給料賃金</th>
      </tr>
    </thead>
    <tbody>
      {if !$form.page2.months}
        <tr><td colspan="4" class="empty">月次データの登録がありません。</td></tr>
      {else}
        {foreach from=$form.page2.months item=m}
          <tr>
            <td class="month">{$m.month|escape}月</td>
            <td class="amount">{$m.sales|escape}</td>
            <td class="amount">{$m.purchase|escape}</td>
            <td class="amount">{$m.salary|escape}</td>
          </tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="month">合計</td>
        <td class="amount">{$form.page2.totals.sales|default:'0'|escape}</td>
        <td class="amount">{$form.page2.totals.purchase|default:'0'|escape}</td>
        <td class="amount">{$form.page2.totals.salary|default:'0'|escape}</td>
      </tr>
    </tbody>
  </table>

  <div class="footer">Rucaro Accounting — 青色申告決算書 月別内訳</div>
</div>
