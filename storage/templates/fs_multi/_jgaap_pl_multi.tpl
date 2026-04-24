{* Multi-period Profit & Loss (Wave 6-I). Same column layout as BS. *}
<table class="multi-table">
  <thead>
    <tr>
      <th class="label">項目</th>
      {foreach $multi.columns as $col}
        <th class="amount">
          {$col.label|escape}<br>
          <span style="font-size:7.5pt; color:#666;">{$col.fromDate|escape} 〜 {$col.toDate|escape}</span>
        </th>
      {/foreach}
      {if $multi.showVariance}
        <th class="amount variance">増減</th>
        <th class="amount variance">増減率</th>
      {/if}
    </tr>
  </thead>
  <tbody>
    {foreach $multi.plRows as $row}
      <tr class="{if $row.isTotal}total{elseif $row.isSubtotal}subtotal{/if}">
        <td class="label">{$row.label|escape}</td>
        {foreach $multi.columns as $col}
          <td class="amount">{$row.amounts[$col.fiscalTermId]|default:'0'|escape}</td>
        {/foreach}
        {if $multi.showVariance}
          <td class="amount variance">{if $row.variance !== null}{$row.variance|escape}{else}-{/if}</td>
          <td class="amount variance">{if $row.variancePercent !== null}{$row.variancePercent|escape}{else}-{/if}</td>
        {/if}
      </tr>
    {/foreach}
  </tbody>
</table>
