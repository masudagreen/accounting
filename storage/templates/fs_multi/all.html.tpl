{extends file="layout.html.tpl"}
{block name="content"}
  {if $hasBs}
    <h2>複数期比較 貸借対照表</h2>
    {include file="_jgaap_bs_multi.tpl"}
  {/if}
  {if $hasPl}
    <h2>複数期比較 損益計算書</h2>
    {include file="_jgaap_pl_multi.tpl"}
  {/if}
  {if $hasCs}
    <h2>複数期比較 キャッシュフロー計算書</h2>
    {include file="_jgaap_cs_multi.tpl"}
  {/if}
{/block}
