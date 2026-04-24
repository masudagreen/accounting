{extends file="layout.html.tpl"}

{block name="content"}
  <header class="mb-4">
    <h1 class="h3 mb-1 text-danger">
      <i class="bi bi-exclamation-triangle-fill"></i> 会計期の削除確認
    </h1>
    <p class="text-muted mb-0">会計期は物理削除されます。関連する仕訳が存在する場合は削除できません。</p>
  </header>

  <div class="rucaro-card p-4 shadow-sm border-danger">
    <dl class="row g-2 mb-4">
      <dt class="col-sm-3 text-muted">ID</dt>
      <dd class="col-sm-9"><code>{$target.id|escape}</code></dd>
      <dt class="col-sm-3 text-muted">期番号</dt>
      <dd class="col-sm-9"><strong>第 {$target.fiscalPeriod} 期</strong></dd>
      <dt class="col-sm-3 text-muted">期間</dt>
      <dd class="col-sm-9"><code>{$target.startDate|escape}</code> 〜 <code>{$target.endDate|escape}</code></dd>
      <dt class="col-sm-3 text-muted">状態</dt>
      <dd class="col-sm-9">{if $target.isClosed}<span class="badge text-bg-secondary">締切済</span>{else}<span class="badge text-bg-success">オープン</span>{/if}</dd>
    </dl>

    <div class="alert alert-danger small mb-3">
      <i class="bi bi-exclamation-circle-fill"></i>
      この会計期を <strong>本当に削除</strong> しますか？ この操作は取り消せません。
    </div>

    <form method="post" action="/ui/masters/fiscal-terms/{$target.id|escape}/delete" class="d-flex gap-2 justify-content-end">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <a href="/ui/masters/fiscal-terms" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-trash"></i> 削除実行
      </button>
    </form>
  </div>
{/block}
