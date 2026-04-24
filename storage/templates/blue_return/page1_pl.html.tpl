<div class="page">
  <h1>青色申告決算書 (1/4) 損益計算書</h1>
  <div class="meta">
    <span>EntityID: {$form.entityId|escape}</span>
    <span>FiscalTermID: {$form.fiscalTermId|escape}</span>
    <span>状態:
      <span class="status-badge status-{$form.status|escape}">{$form.status|escape|upper}</span>
      <span class="form-type-badge">{$form.formType|escape}</span>
    </span>
    <span>生成日時: {$form.generatedAt|escape}</span>
  </div>

  <h2>収入金額</h2>
  <table class="br-table">
    <thead><tr><th style="width: 70%">科目</th><th>金額</th></tr></thead>
    <tbody>
      {if !$form.page1.revenue}
        <tr><td colspan="2" class="empty">収入金額の登録がありません。</td></tr>
      {else}
        {foreach from=$form.page1.revenue item=r}
          <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="label">収入金額合計</td>
        <td class="amount">{$form.page1.revenueTotal|default:'0'|escape}</td>
      </tr>
    </tbody>
  </table>

  <h2>売上原価</h2>
  <table class="br-table">
    <thead><tr><th style="width: 70%">科目</th><th>金額</th></tr></thead>
    <tbody>
      {if !$form.page1.costOfSales}
        <tr><td colspan="2" class="empty">売上原価の登録がありません。</td></tr>
      {else}
        {foreach from=$form.page1.costOfSales item=r}
          <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="label">売上原価合計</td>
        <td class="amount">{$form.page1.costOfSalesTotal|default:'0'|escape}</td>
      </tr>
    </tbody>
  </table>

  <h2>経費</h2>
  <table class="br-table">
    <thead><tr><th style="width: 70%">科目</th><th>金額</th></tr></thead>
    <tbody>
      {if !$form.page1.expenses}
        <tr><td colspan="2" class="empty">経費の登録がありません。</td></tr>
      {else}
        {foreach from=$form.page1.expenses item=r}
          <tr><td class="label">{$r.label|escape}</td><td class="amount">{$r.amount|escape}</td></tr>
        {/foreach}
      {/if}
      <tr class="total-row">
        <td class="label">経費合計</td>
        <td class="amount">{$form.page1.expensesTotal|default:'0'|escape}</td>
      </tr>
    </tbody>
  </table>

  <table class="br-table">
    <tbody>
      <tr class="net-row">
        <td class="label" style="width: 70%">所得金額（収入 − 売上原価 − 経費）</td>
        <td class="amount">{$form.page1.netIncome|default:'0'|escape}</td>
      </tr>
    </tbody>
  </table>

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-H-1 / 青色申告決算書 (個人事業主)
    {if !$hasJapaneseFont}<div class="note">※ IPAex Gothic 未インストール。一部文字が欠落する場合があります。</div>{/if}
  </div>
</div>
