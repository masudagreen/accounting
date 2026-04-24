{extends file="layout.html.tpl"}
{block name="content"}
  {if $hasBs}
    <h1 style="font-size:14pt; margin-top:10pt;">貸借対照表</h1>
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
  {/if}

  {if $hasPl}
    <h1 style="font-size:14pt; margin-top:14pt;">損益計算書</h1>
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
  {/if}

  {if $hasCs}
    <h1 style="font-size:14pt; margin-top:14pt;">キャッシュフロー計算書</h1>
    {if $hasJgaapCs}
      {include file="_jgaap_cs.tpl"}
    {else}
      {if isset($fs.cs.operating)}
        {include file="_section.tpl" section=$fs.cs.operating}
      {/if}
      {if isset($fs.cs.investing)}
        {include file="_section.tpl" section=$fs.cs.investing}
      {/if}
      {if isset($fs.cs.financing)}
        {include file="_section.tpl" section=$fs.cs.financing}
      {/if}
    {/if}
  {/if}

  <div class="totals">
    <span class="label">当期純利益</span>
    <span class="value">{$fs.totals.net_income|default:'0.0000'|escape}</span>
  </div>
{/block}
