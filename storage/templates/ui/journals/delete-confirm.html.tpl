{extends file="layout.html.tpl"}

{block name="content"}
  <header class="mb-4">
    <h1 class="h3 mb-1 text-danger">
      <i class="bi bi-exclamation-triangle-fill"></i> 仕訳の削除確認
    </h1>
    <p class="text-muted mb-0">この操作は取り消せません。内容を確認してから実行してください。</p>
  </header>

  <div class="rucaro-card p-4 shadow-sm border-danger">
    <dl class="row g-2 mb-4">
      <dt class="col-sm-2 text-muted">仕訳 ID</dt>
      <dd class="col-sm-10"><code>{$journal.id|escape}</code></dd>
      <dt class="col-sm-2 text-muted">発生日</dt>
      <dd class="col-sm-4"><code>{$journal.journalDate|escape}</code></dd>
      <dt class="col-sm-2 text-muted">合計金額</dt>
      <dd class="col-sm-4">{$journal.totalAmount|escape}</dd>
      <dt class="col-sm-2 text-muted">摘要</dt>
      <dd class="col-sm-10">{$journal.summary|default:'（摘要なし）'|escape}</dd>
      <dt class="col-sm-2 text-muted">状態</dt>
      <dd class="col-sm-10"><span class="badge text-bg-warning">{$journal.status|escape}</span></dd>
    </dl>

    <h2 class="h6 mb-2">明細行</h2>
    <div class="table-responsive mb-4">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>借/貸</th>
            <th>勘定科目</th>
            <th class="text-end">金額</th>
            <th>メモ</th>
          </tr>
        </thead>
        <tbody>
          {foreach $lines as $line}
            <tr>
              <td>{if $line.side == 'debit'}借方{else}貸方{/if}</td>
              <td><code>{$line.account_title_id|escape}</code></td>
              <td class="text-end">{$line.amount|escape}</td>
              <td>{$line.memo|escape}</td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="alert alert-danger small mb-3">
      <i class="bi bi-exclamation-circle-fill"></i>
      この仕訳を <strong>本当に削除</strong> しますか？ ドラフト以外の仕訳は削除できません。
    </div>

    <form method="post" action="/ui/journals/{$journal.id|escape}/delete" class="d-flex gap-2 justify-content-end">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <a href="/ui/journals/{$journal.id|escape}" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-trash"></i> 削除実行
      </button>
    </form>
  </div>
{/block}
