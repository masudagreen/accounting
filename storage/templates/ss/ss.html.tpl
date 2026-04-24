{extends file="layout.html.tpl"}

{block name="header"}
  <div class="meta">
    <span>EntityID: {$ss.entityId|escape}</span>
    <span>FiscalTermID: {$ss.fiscalTermId|escape}</span>
    <span>対象期間: {$ss.fromDate|escape} 〜 {$ss.toDate|escape}</span>
    <span>通貨: {$ss.currencyCode|escape}</span>
    <span>生成日時: {$ss.generatedAt|escape}</span>
  </div>
{/block}

{block name="content"}
  <table class="ss-table">
    <thead>
      <tr>
        <th rowspan="2" style="width: 18%">変動事由</th>
        {foreach from=$ss.columns item=col}
          <th class="section">{$col.label|escape}</th>
        {/foreach}
        <th class="section total-col">合計</th>
      </tr>
    </thead>
    <tbody>
      <tr class="opening-row">
        <td class="label">期首残高</td>
        {foreach from=$ss.columns item=col}
          <td class="amount">{$col.openingBalance|escape}</td>
        {/foreach}
        <td class="amount total-col">{$ss.totals.opening|escape}</td>
      </tr>

      {if !$ss.rows}
        <tr class="change-row">
          <td class="label">（当期変動なし）</td>
          {foreach from=$ss.columns item=col}
            <td class="amount">-</td>
          {/foreach}
          <td class="amount total-col">-</td>
        </tr>
      {else}
        {foreach from=$ss.rows item=row}
          <tr class="change-row">
            <td class="label">{$row.label|escape}</td>
            {foreach from=$row.cells item=cell}
              <td class="amount source-{$cell.source|escape}">{$cell.amount|escape}</td>
            {/foreach}
            <td class="amount total-col">-</td>
          </tr>
        {/foreach}
      {/if}

      <tr class="total-change-row">
        <td class="label">当期変動額合計</td>
        {foreach from=$ss.columns item=col}
          <td class="amount">{$col.totalChange|escape}</td>
        {/foreach}
        <td class="amount total-col">{$ss.totals.totalChange|escape}</td>
      </tr>

      <tr class="ending-row">
        <td class="label">期末残高</td>
        {foreach from=$ss.columns item=col}
          <td class="amount">{$col.endingBalance|escape}</td>
        {/foreach}
        <td class="amount total-col">{$ss.totals.ending|escape}</td>
      </tr>
    </tbody>
  </table>

  <div class="legend">
    <span><span class="auto-marker">■</span> 緑字: journal から自動算出した変動 (当期純利益など)</span>
    <span>黒字: 手動入力の調整 (配当、新株発行、自己株式、評価換算差額等)</span>
  </div>
{/block}
