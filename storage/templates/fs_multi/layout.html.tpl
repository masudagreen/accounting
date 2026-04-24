<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'複数期比較 決算書'|escape} — Rucaro Accounting</title>
<style>
{include file="fs-multi-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'複数期比較 決算書'|escape}</h1>
    <div class="meta">
      <span>期数: {$multi.columns|count}</span>
      <span>生成日時: {$multi.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-I / EntityID: {$multi.entityId|escape}
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
