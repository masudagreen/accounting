<div class="page">
  <h1>青色申告決算書 (3/4) 内訳</h1>

  <h2>減価償却費の内訳</h2>
  <table class="br-table">
    <thead>
      <tr>
        <th>資産名</th>
        <th>取得価額</th>
        <th>償却方法</th>
        <th>耐用年数</th>
        <th>当期償却費</th>
      </tr>
    </thead>
    <tbody>
      {if !$form.page3.depreciation}
        <tr><td colspan="5" class="empty">減価償却資産の登録がありません。</td></tr>
      {else}
        {foreach from=$form.page3.depreciation item=d}
          <tr>
            <td class="label">{$d.name|default:''|escape}</td>
            <td class="amount">{$d.acquisitionCost|default:'0'|escape}</td>
            <td class="label">{$d.method|default:''|escape}</td>
            <td class="amount">{$d.usefulLifeYears|default:''|escape}</td>
            <td class="amount">{$d.periodDepreciation|default:'0'|escape}</td>
          </tr>
        {/foreach}
      {/if}
    </tbody>
  </table>

  <div class="half-row">
    <div class="half-col">
      <h2>貸倒引当金</h2>
      <table class="br-table">
        <thead><tr><th>区分</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page3.allowance}
            <tr><td colspan="2" class="empty">なし</td></tr>
          {else}
            {foreach from=$form.page3.allowance item=a}
              <tr><td class="label">{$a.label|default:''|escape}</td><td class="amount">{$a.amount|default:'0'|escape}</td></tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
    <div class="half-col">
      <h2>地代家賃</h2>
      <table class="br-table">
        <thead><tr><th>支払先</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page3.rent}
            <tr><td colspan="2" class="empty">なし</td></tr>
          {else}
            {foreach from=$form.page3.rent item=r}
              <tr><td class="label">{$r.label|default:''|escape}</td><td class="amount">{$r.amount|default:'0'|escape}</td></tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
  </div>

  <div class="half-row">
    <div class="half-col">
      <h2>利子割引料</h2>
      <table class="br-table">
        <thead><tr><th>支払先</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page3.interest}
            <tr><td colspan="2" class="empty">なし</td></tr>
          {else}
            {foreach from=$form.page3.interest item=r}
              <tr><td class="label">{$r.label|default:''|escape}</td><td class="amount">{$r.amount|default:'0'|escape}</td></tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
    <div class="half-col">
      <h2>税理士・弁護士報酬</h2>
      <table class="br-table">
        <thead><tr><th>支払先</th><th>金額</th></tr></thead>
        <tbody>
          {if !$form.page3.taxAccountant}
            <tr><td colspan="2" class="empty">なし</td></tr>
          {else}
            {foreach from=$form.page3.taxAccountant item=r}
              <tr><td class="label">{$r.label|default:''|escape}</td><td class="amount">{$r.amount|default:'0'|escape}</td></tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
  </div>

  <div class="footer">Rucaro Accounting — 青色申告決算書 内訳書</div>
</div>
