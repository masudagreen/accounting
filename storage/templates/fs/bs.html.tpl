{extends file="layout.html.tpl"}
{block name="content"}
  {if $hasJgaap}
    {include file="_jgaap_bs.tpl"}
  {else}
    <table class="bs-grid"><tbody><tr>
      <td>
        {if isset($fs.bs.assets)}
          {include file="_section.tpl" section=$fs.bs.assets}
        {/if}
      </td>
      <td>
        {if isset($fs.bs.liabilities)}
          {include file="_section.tpl" section=$fs.bs.liabilities}
        {/if}
        {if isset($fs.bs.equity)}
          {include file="_section.tpl" section=$fs.bs.equity}
        {/if}
      </td>
    </tr></tbody></table>
  {/if}

  <div class="totals">
    <span class="label">資産合計</span>
    <span class="value">{$fs.totals.total_assets|default:'0.0000'|escape}</span>
    <span class="label">負債合計</span>
    <span class="value">{$fs.totals.total_liabilities|default:'0.0000'|escape}</span>
    <span class="label">純資産合計</span>
    <span class="value">{$fs.totals.total_equity|default:'0.0000'|escape}</span>
  </div>
{/block}
