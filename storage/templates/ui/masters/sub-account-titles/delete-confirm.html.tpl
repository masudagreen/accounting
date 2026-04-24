{extends file="layout.html.tpl"}

{block name="content"}
  <header class="mb-4">
    <h1 class="h3 mb-1 text-danger">
      <i class="bi bi-exclamation-triangle-fill"></i> 補助科目の削除確認
    </h1>
    <p class="text-muted mb-0">論理削除を行います。内容を確認してから実行してください。</p>
  </header>

  <div class="rucaro-card p-4 shadow-sm border-danger">
    <dl class="row g-2 mb-4">
      <dt class="col-sm-3 text-muted">ID</dt>
      <dd class="col-sm-9"><code>{$target.id|escape}</code></dd>
      <dt class="col-sm-3 text-muted">親勘定科目</dt>
      <dd class="col-sm-9"><code>{$target.parentCode|escape}</code> <span class="text-muted">— {$target.parentName|escape}</span></dd>
      <dt class="col-sm-3 text-muted">コード</dt>
      <dd class="col-sm-9"><code>{$target.code|escape}</code></dd>
      <dt class="col-sm-3 text-muted">名称</dt>
      <dd class="col-sm-9">{$target.name|escape}</dd>
    </dl>

    <div class="alert alert-danger small mb-3">
      <i class="bi bi-exclamation-circle-fill"></i>
      この補助科目を <strong>本当に削除</strong> しますか？
    </div>

    <form method="post" action="/ui/masters/sub-account-titles/{$target.id|escape}/delete" class="d-flex gap-2 justify-content-end">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <a href="/ui/masters/sub-account-titles" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-trash"></i> 削除実行
      </button>
    </form>
  </div>
{/block}
