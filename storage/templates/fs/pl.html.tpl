{extends file="layout.html.tpl"}
{block name="content"}
  {if $hasJgaap}
    {include file="_jgaap_pl.tpl"}
  {else}
    {if isset($fs.pl.revenue)}
      {include file="_section.tpl" section=$fs.pl.revenue}
    {/if}
    {if isset($fs.pl.expenses)}
      {include file="_section.tpl" section=$fs.pl.expenses}
    {/if}
  {/if}
  <div class="totals">
    <span class="label">収益合計</span>
    <span class="value">{$fs.totals.total_revenue|default:'0.0000'|escape}</span>
    <span class="label">費用合計</span>
    <span class="value">{$fs.totals.total_expenses|default:'0.0000'|escape}</span>
    <span class="label">当期純利益</span>
    <span class="value">{$fs.totals.net_income|default:'0.0000'|escape}</span>
  </div>
{/block}
