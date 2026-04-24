<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'株主資本等変動計算書'|escape} — Rucaro Accounting</title>
<style>
{include file="ss-common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'株主資本等変動計算書'|escape}</h1>
    {block name="header"}{/block}
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-H-2 / 株主資本等変動計算書
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
