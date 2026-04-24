{* Renders a Section as a two-column table: label | amount. *}
{if isset($section)}
  <h2>{$section.label|escape}</h2>
  <table class="fs-table">
    <thead>
      <tr>
        <th style="width:70%;">勘定科目</th>
        <th class="amount" style="width:30%;">金額</th>
      </tr>
    </thead>
    <tbody>
    {foreach $section.lines as $line}
      <tr{if $line.isSubtotal} class="subtotal"{/if}>
        <td>{if $line.depth > 1}&nbsp;&nbsp;{/if}{$line.label|escape}</td>
        <td class="amount">{$line.amount|escape}</td>
      </tr>
    {foreachelse}
      <tr>
        <td colspan="2" style="color:#888;">(該当なし)</td>
      </tr>
    {/foreach}
      <tr class="subtotal">
        <td>小計</td>
        <td class="amount">{$section.subtotal|escape}</td>
      </tr>
    </tbody>
  </table>
{/if}
