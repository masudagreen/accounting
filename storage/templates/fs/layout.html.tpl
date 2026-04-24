<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'決算書'|escape} — Rucaro Accounting</title>
<style>
{include file="fs-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'決算書'|escape}</h1>
    <div class="meta">
      <span>対象期間: {$fs.fromDate|escape} 〜 {$fs.toDate|escape}</span>
      <span>通貨: {$fs.currencyCode|escape}</span>
      <span>生成日時: {$fs.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6.6 / EntityID: {$fs.entityId|escape}
    / FiscalTermID: {$fs.fiscalTermId|escape}
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
