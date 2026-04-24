<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'注記表'|escape} — Rucaro Accounting</title>
<style>
{include file="fs-notes-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'注記表'|escape}</h1>
    {block name="header"}{/block}
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-H-3 / 注記表
    {if !$hasJapaneseFont}
      <div class="notice">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
