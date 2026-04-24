<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'資金繰り表'|escape} — Rucaro Accounting</title>
<style>
{include file="cash-plan-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'資金繰り表'|escape}</h1>
    <div class="meta">
      <span>EntityID: {$plan.entityId|escape}</span>
      <span>FiscalTermID: {$plan.fiscalTermId|escape}</span>
      <span>名称: {$plan.name|escape}</span>
      <span>通貨: {$plan.currencyCode|escape}</span>
      <span>生成日時: {$plan.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-E / 資金繰り表
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
