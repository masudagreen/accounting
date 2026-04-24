<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'固定資産台帳'|escape} — Rucaro Accounting</title>
<style>
{include file="ledger-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'固定資産台帳'|escape}</h1>
    <div class="meta">
      <span>EntityID: {$ledger.entityId|escape}</span>
      {if $ledger.fiscalTermId}<span>FiscalTermID: {$ledger.fiscalTermId|escape}</span>{/if}
      <span>生成日時: {$ledger.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-D / 固定資産台帳
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
