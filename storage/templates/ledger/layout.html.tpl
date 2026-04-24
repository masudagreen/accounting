<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'総勘定元帳'|escape} — Rucaro Accounting</title>
<style>
{include file="ledger-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'総勘定元帳'|escape}</h1>
    <div class="meta">
      <span>対象期間: {$ledger.fromDate|escape} 〜 {$ledger.toDate|escape}</span>
      <span>通貨: {$ledger.currencyCode|escape}</span>
      <span>生成日時: {$ledger.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-C / EntityID: {$ledger.entityId|escape}
    / FiscalTermID: {$ledger.fiscalTermId|escape}
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
