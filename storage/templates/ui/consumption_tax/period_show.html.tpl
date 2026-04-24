{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">消費税申告期間</h1>
      <p class="text-muted mb-0">期間 <code>{$period.periodFrom|escape}</code> 〜 <code>{$period.periodTo|escape}</code></p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/consumption-tax/periods"><i class="bi bi-arrow-left"></i> 一覧へ戻る</a>
    </div>
  </header>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <dl class="row g-2 mb-0">
      <dt class="col-sm-2 text-muted">課税方式</dt>
      <dd class="col-sm-4">{$period.methodLabel|escape}</dd>
      <dt class="col-sm-2 text-muted">事業区分</dt>
      <dd class="col-sm-4">{$period.simplifiedLabel|default:'—'|escape}</dd>
      <dt class="col-sm-2 text-muted">中間申告</dt>
      <dd class="col-sm-4">{if $period.isInterim}はい{else}いいえ{/if}</dd>
      <dt class="col-sm-2 text-muted">ステータス</dt>
      <dd class="col-sm-4"><span class="badge text-bg-secondary">{$period.status|escape}</span></dd>
      <dt class="col-sm-2 text-muted">確定日時</dt>
      <dd class="col-sm-10">{$period.settledAt|default:'—'|escape}</dd>
    </dl>
  </section>

  <section class="rucaro-card p-4 shadow-sm">
    <h2 class="h5 mb-3">操作</h2>
    <div class="d-flex gap-2 flex-wrap">
      <form method="post" action="/ui/consumption-tax/periods/{$period.id|escape}/calculate">
        <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-calculator"></i> 消費税を計算</button>
      </form>
      <a href="/ui/consumption-tax/periods/{$period.id|escape}/report" class="btn btn-outline-success">
        <i class="bi bi-file-earmark-text"></i> 申告書を表示
      </a>
    </div>
  </section>
{/block}
