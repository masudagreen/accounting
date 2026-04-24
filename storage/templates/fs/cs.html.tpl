{extends file="layout.html.tpl"}
{block name="content"}
  {if $hasJgaapCs}
    {include file="_jgaap_cs.tpl"}
  {else}
    <div class="note">
      ※ CS（キャッシュフロー計算書）は簡易版（間接法ベース）です。
      勘定科目 → CS 区分のマッピングが未設定の場合、ここでは当期純利益と
      現預金の増減のみを営業 CF 配下に表示します。
      フル構造での出力には `account_title_cs_mappings` のシードが必要です。
    </div>
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
  <div class="totals">
    <span class="label">当期純利益</span>
    <span class="value">{$fs.totals.net_income|default:'0.0000'|escape}</span>
  </div>
{/block}
