<div class="page">
  <h1>青色申告決算書 (4/4) 貸借対照表</h1>

  <div class="half-row">
    <div class="half-col">
      <h2>資産の部</h2>
      <table class="br-table">
        <thead><tr><th style="width:65%">科目</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page4.assets}
            <tr><td colspan="2" class="empty">資産の登録がありません。</td></tr>
          {else}
            {foreach from=$form.page4.assets item=r}
              <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
            {/foreach}
          {/if}
          <tr class="total-row">
            <td class="label">資産合計</td>
            <td class="amount">{$form.page4.assetsTotal|default:'0'|escape}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="half-col">
      <h2>負債の部</h2>
      <table class="br-table">
        <thead><tr><th style="width:65%">科目</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page4.liabilities}
            <tr><td colspan="2" class="empty">負債の登録がありません。</td></tr>
          {else}
            {foreach from=$form.page4.liabilities item=r}
              <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
            {/foreach}
          {/if}
          <tr class="total-row">
            <td class="label">負債合計</td>
            <td class="amount">{$form.page4.liabilitiesTotal|default:'0'|escape}</td>
          </tr>
        </tbody>
      </table>

      <h2>元入金の部</h2>
      <table class="br-table">
        <thead><tr><th style="width:65%">科目</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page4.equity}
            <tr><td colspan="2" class="empty">元入金の登録がありません。</td></tr>
          {else}
            {foreach from=$form.page4.equity item=r}
              <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
            {/foreach}
          {/if}
          <tr class="total-row">
            <td class="label">元入金合計</td>
            <td class="amount">{$form.page4.equityTotal|default:'0'|escape}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="footer">Rucaro Accounting — 青色申告決算書 貸借対照表 (個人版)</div>
</div>
