{* J-GAAP indirect-method Cash Flow Statement (Phase 6 Wave 6-B port).

   I.   営業活動によるキャッシュフロー
   II.  投資活動によるキャッシュフロー
   III. 財務活動によるキャッシュフロー
   + 現金及び現金同等物の増減 / 期首 / 期末

   Uses $csOrder from DompdfFinancialStatementGenerator::csOrder() so the
   template does not have to know about specific section codes.
*}
<table class="fs-table">
  <thead>
    <tr><th style="width:70%;">項目</th><th class="amount" style="width:30%;">金額</th></tr>
  </thead>
  <tbody>
    {foreach $csOrder as $row}
      {if isset($fs.cs[$row.code])}
        {if $row.isTotal}
          <tr class="total">
            <td class="indent-{$row.indent}">{$row.label|escape}</td>
            <td class="amount">{$fs.cs[$row.code].subtotal|escape}</td>
          </tr>
        {elseif $row.isSubtotal}
          <tr class="subtotal">
            <td class="indent-{$row.indent}">{$row.label|escape}</td>
            <td class="amount">{$fs.cs[$row.code].subtotal|escape}</td>
          </tr>
        {else}
          <tr>
            <td class="indent-{$row.indent}">{$row.label|escape}</td>
            <td class="amount">{$fs.cs[$row.code].subtotal|escape}</td>
          </tr>
          {foreach $fs.cs[$row.code].lines as $line}
            <tr class="indent-2">
              <td>{$line.label|escape}</td>
              <td class="amount">{$line.amount|escape}</td>
            </tr>
          {/foreach}
        {/if}
      {/if}
    {/foreach}
  </tbody>
</table>
