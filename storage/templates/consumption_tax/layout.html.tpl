<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title|default:'消費税申告書イメージ'|escape} — Rucaro Accounting</title>
<style>
{include file="common.css.tpl"}
</style>
</head>
<body>
  <header>
    <h1>{$title|default:'消費税申告書イメージ'|escape}</h1>
    <div class="meta">
      <span>Period: {$report.period.from|escape} 〜 {$report.period.to|escape}</span>
      <span>計算方式: {$report.period.method|escape}</span>
      {if $report.period.simplifiedBusinessCategory}
        <span>事業区分: {$report.period.simplifiedBusinessCategory|escape}</span>
      {/if}
      <span>状態: {$report.period.status|escape}</span>
      <span>生成日時: {$report.generatedAt|escape}</span>
    </div>
  </header>

  {block name="content"}{/block}

  <div class="footer">
    Rucaro Accounting — Phase 6 Wave 6-F / 消費税申告書
    {if !$hasJapaneseFont}
      <div class="note">※ 日本語フォント (IPAex Gothic) 未インストールのため、一部文字が欠落する場合があります。</div>
    {/if}
  </div>
</body>
</html>
