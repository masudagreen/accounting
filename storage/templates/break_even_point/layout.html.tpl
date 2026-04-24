<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'損益分岐点分析'|escape} — Rucaro Accounting</title>
<style>
{include file="bep-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'損益分岐点分析'|escape}</h1>
    <div class="meta">
      <span>EntityID: {$analysis.entityId|escape}</span>
      <span>FiscalTermID: {$analysis.fiscalTermId|escape}</span>
      <span>対象期間: {$analysis.fromDate|escape} 〜 {$analysis.toDate|escape}</span>
      <span>通貨: {$analysis.currencyCode|escape}</span>
      <span>生成日時: {$analysis.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-E / 損益分岐点 (CVP) 分析
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
