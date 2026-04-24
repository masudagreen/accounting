{extends file="layout.html.tpl"}

{block name="content"}
  <header class="mb-4">
    <h1 class="h3 mb-1 text-danger">
      <i class="bi bi-exclamation-triangle-fill"></i> 事業主の削除確認
    </h1>
    <p class="text-muted mb-0">論理削除を行います。内容を確認してから実行してください。</p>
  </header>

  <div class="rucaro-card p-4 shadow-sm border-danger">
    <dl class="row g-2 mb-4">
      <dt class="col-sm-3 text-muted">ID</dt>
      <dd class="col-sm-9"><code>{$target.id|escape}</code></dd>
      <dt class="col-sm-3 text-muted">名称</dt>
      <dd class="col-sm-9">{$target.name|escape}</dd>
      <dt class="col-sm-3 text-muted">国コード</dt>
      <dd class="col-sm-9"><code>{$target.nationCode|escape}</code></dd>
      <dt class="col-sm-3 text-muted">通貨コード</dt>
      <dd class="col-sm-9"><code>{$target.currencyCode|escape}</code></dd>
    </dl>

    <div class="alert alert-danger small mb-3">
      <i class="bi bi-exclamation-circle-fill"></i>
      この事業主を <strong>本当に削除</strong> しますか？ 関連する仕訳や勘定科目が残っている場合は削除できません。
    </div>

    <form method="post" action="/ui/masters/entities/{$target.id|escape}/delete" class="d-flex gap-2 justify-content-end">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <a href="/ui/masters/entities" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-trash"></i> 削除実行
      </button>
    </form>
  </div>
{/block}
