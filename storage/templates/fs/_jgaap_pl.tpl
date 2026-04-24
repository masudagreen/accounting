{* J-GAAP step-wise Profit & Loss (Phase 6-A port).

   売上高 → 売上原価 → 売上総利益 → 販管費 → 営業利益 → 営業外収益
   → 営業外費用 → 経常利益 → 特別利益 → 特別損失 → 税引前当期純利益
   → 法人税等 → 当期純利益
*}
<table class="fs-table">
  <thead>
    <tr><th style="width:70%;">項目</th><th class="amount" style="width:30%;">金額</th></tr>
  </thead>
  <tbody>
    {foreach $plOrder as $row}
      {if isset($fs.pl[$row.code])}
        {if $row.isSubtotal || $row.isTotal}
          <tr class="{if $row.isTotal}total{else}subtotal{/if}">
            <td>{$row.label|escape}</td>
            <td class="amount">{$fs.pl[$row.code].subtotal|escape}</td>
          </tr>
        {else}
          <tr class="subtotal">
            <td>{$row.label|escape}</td>
            <td class="amount">{$fs.pl[$row.code].subtotal|escape}</td>
          </tr>
          {foreach $fs.pl[$row.code].lines as $line}
            <tr class="indent-1">
              <td>{$line.label|escape}</td>
              <td class="amount">{$line.amount|escape}</td>
            </tr>
          {/foreach}
        {/if}
      {/if}
    {/foreach}
  </tbody>
</table>
