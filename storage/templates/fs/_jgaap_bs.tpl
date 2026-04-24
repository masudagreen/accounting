{* J-GAAP hierarchical Balance Sheet (Phase 6-A port).

   Renders traditional T-form BS using fs_section_definitions codes seeded
   by `scripts/migrate/0008_fs_mappings_seed.sql`. Falls back silently when
   the port-style sections aren't present in the view model so the same
   template works with both simplified and port outputs.
*}
<table class="bs-grid"><tbody><tr>
  <td>
    <h2>資産の部</h2>
    <table class="fs-table">
      <tbody>
        {foreach $bsOrder.assetGroups as $grp}
          {if isset($fs.bs[$grp.code])}
            <tr class="subtotal"><td>{$grp.label|escape}</td>
              <td class="amount">{$fs.bs[$grp.code].subtotal|escape}</td></tr>
            {foreach $fs.bs[$grp.code].lines as $line}
              <tr class="indent-1">
                <td>{$line.label|escape}</td>
                <td class="amount">{$line.amount|escape}</td>
              </tr>
            {/foreach}
          {/if}
        {/foreach}
        {if isset($fs.bs.asset_total)}
          <tr class="total"><td>資産合計</td>
            <td class="amount">{$fs.bs.asset_total.subtotal|escape}</td></tr>
        {/if}
      </tbody>
    </table>
  </td>
  <td>
    <h2>負債の部</h2>
    <table class="fs-table">
      <tbody>
        {foreach $bsOrder.liabilityGroups as $grp}
          {if isset($fs.bs[$grp.code])}
            <tr class="subtotal"><td>{$grp.label|escape}</td>
              <td class="amount">{$fs.bs[$grp.code].subtotal|escape}</td></tr>
            {foreach $fs.bs[$grp.code].lines as $line}
              <tr class="indent-1">
                <td>{$line.label|escape}</td>
                <td class="amount">{$line.amount|escape}</td>
              </tr>
            {/foreach}
          {/if}
        {/foreach}
        {if isset($fs.bs.liability_total)}
          <tr class="total"><td>負債合計</td>
            <td class="amount">{$fs.bs.liability_total.subtotal|escape}</td></tr>
        {/if}
      </tbody>
    </table>

    <h2>純資産の部</h2>
    <table class="fs-table">
      <tbody>
        {foreach $bsOrder.equityGroups as $grp}
          {if isset($fs.bs[$grp.code])}
            <tr class="subtotal"><td>{$grp.label|escape}</td>
              <td class="amount">{$fs.bs[$grp.code].subtotal|escape}</td></tr>
            {foreach $fs.bs[$grp.code].lines as $line}
              <tr class="indent-1">
                <td>{$line.label|escape}</td>
                <td class="amount">{$line.amount|escape}</td>
              </tr>
            {/foreach}
          {/if}
        {/foreach}
        {if isset($fs.bs.equity_total)}
          <tr class="total"><td>純資産合計</td>
            <td class="amount">{$fs.bs.equity_total.subtotal|escape}</td></tr>
        {/if}
      </tbody>
    </table>
  </td>
</tr></tbody></table>
