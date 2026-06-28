{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">仕訳詳細</h1>
      <p class="text-muted mb-0">
        <code>{$form_journal.id|escape}</code>
        {assign var="badge" value="text-bg-secondary"}
        {if $form_journal.status == 'draft'}{assign var="badge" value="text-bg-warning"}{/if}
        {if $form_journal.status == 'posted'}{assign var="badge" value="text-bg-success"}{/if}
        {if $form_journal.status == 'approved'}{assign var="badge" value="text-bg-info"}{/if}
        {if $form_journal.status == 'rejected'}{assign var="badge" value="text-bg-danger"}{/if}
        <span class="badge {$badge} ms-2">{$form_journal.status|escape}</span>
      </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary btn-sm" href="/ui/journals">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
      {if $can_edit}
        <a class="btn btn-primary btn-sm" href="/ui/journals/{$form_journal.id|escape}/edit">
          <i class="bi bi-pencil"></i> 編集
        </a>
      {/if}
      {if $can_approve}
        <form method="post" action="/ui/journals/{$form_journal.id|escape}/approve" class="d-inline"
              onsubmit="return confirm('この仕訳を承認しますか？');">
          <input type="hidden" name="_csrf" value="{$csrf_approve_token|escape}">
          <button type="submit" class="btn btn-outline-info btn-sm">
            <i class="bi bi-check2"></i> 承認
          </button>
        </form>
      {/if}
      {if $can_post}
        <form method="post" action="/ui/journals/{$form_journal.id|escape}/post" class="d-inline"
              onsubmit="return confirm('この仕訳を確定（post）します。確定後は変更できません。よろしいですか？');">
          <input type="hidden" name="_csrf" value="{$csrf_post_token|escape}">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-check-circle"></i> 確定 (Post)
          </button>
        </form>
      {/if}
      {if $can_edit}
        <a class="btn btn-outline-danger btn-sm" href="/ui/journals/{$form_journal.id|escape}/delete">
          <i class="bi bi-trash"></i> 削除
        </a>
      {/if}
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">
      {foreach $form_errors['_'] as $msg}<div>{$msg|escape}</div>{/foreach}
    </div>
  {/if}

  {* Show is always read-only — drafts get an 編集 button in the header
     that links to /ui/journals/{id}/edit (a standalone form page using
     the same chrome as 新規仕訳). Lets posted / draft view share one
     read-only twin-table, which keeps the page small and free of the
     edit-form JS payload. *}
  <div class="rucaro-card p-4 shadow-sm">
      <dl class="row g-2 mb-4">
        <dt class="col-sm-2 text-muted">発生日</dt>
        <dd class="col-sm-4"><code>{$form_journal.journalDate|escape}</code></dd>
        <dt class="col-sm-2 text-muted">合計金額</dt>
        <dd class="col-sm-4">{if isset($form_journal.totalAmount)}{$form_journal.totalAmount|escape}{/if}</dd>
        <dt class="col-sm-2 text-muted">摘要</dt>
        <dd class="col-sm-10">{$form_journal.summary|default:'（摘要なし）'|escape}</dd>
        <dt class="col-sm-2 text-muted">起票者</dt>
        <dd class="col-sm-4">{if isset($creator_name) && $creator_name != ''}{$creator_name|escape}{else}<code class="text-muted">{$form_journal.createdBy|escape}</code>{/if}</dd>
        <dt class="col-sm-2 text-muted">作成日時</dt>
        <dd class="col-sm-4">{if isset($form_journal.createdAt)}{$form_journal.createdAt|escape}{/if}</dd>
      </dl>

      {* Read-only twin-table: 借方 left / 貸方 right (簿記の標準位置).
         Mirrors the new-journal form layout so the visual model is
         consistent across create / edit / view. *}
      <h2 class="h6 mb-2">明細</h2>
      <div class="row g-3">
        {* ============ 借方 (Debit) — 左（簿記の標準位置） ============ *}
        <div class="col-lg-6">
          <div class="show-side-card debit">
            <div class="show-side-header">
              <span class="badge text-bg-primary side-badge">借方 (Debit)</span>
            </div>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:32px;">#</th>
                    <th>勘定科目</th>
                    <th class="text-end" style="width:120px;">金額</th>
                    <th>メモ</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach $form_debit_lines as $idx => $line}
                    <tr>
                      <td class="text-center text-muted small">{$idx+1}</td>
                      <td>{if isset($account_name_by_id[$line.account_title_id])}{$account_name_by_id[$line.account_title_id]|escape}{else}<code class="text-muted">{$line.account_title_id|truncate:10:""|escape}…</code>{/if}</td>
                      <td class="text-end">{$line.amount|escape}</td>
                      <td><small>{$line.memo|escape}</small></td>
                    </tr>
                  {foreachelse}
                    <tr><td colspan="4" class="text-center text-muted small py-3">行なし</td></tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {* ============ 貸方 (Credit) — 右（簿記の標準位置） ============ *}
        <div class="col-lg-6">
          <div class="show-side-card credit">
            <div class="show-side-header">
              <span class="badge text-bg-warning side-badge">貸方 (Credit)</span>
            </div>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:32px;">#</th>
                    <th>勘定科目</th>
                    <th class="text-end" style="width:120px;">金額</th>
                    <th>メモ</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach $form_credit_lines as $idx => $line}
                    <tr>
                      <td class="text-center text-muted small">{$idx+1}</td>
                      <td>{if isset($account_name_by_id[$line.account_title_id])}{$account_name_by_id[$line.account_title_id]|escape}{else}<code class="text-muted">{$line.account_title_id|truncate:10:""|escape}…</code>{/if}</td>
                      <td class="text-end">{$line.amount|escape}</td>
                      <td><small>{$line.memo|escape}</small></td>
                    </tr>
                  {foreachelse}
                    <tr><td colspan="4" class="text-center text-muted small py-3">行なし</td></tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <style>
        .show-side-card { border: 1px solid #dee2e6; border-radius: .5rem; padding: .5rem; background: #fff; }
        .show-side-card.credit { border-left: 4px solid #ffc107; background: rgba(255, 193, 7, 0.03); }
        .show-side-card.debit  { border-left: 4px solid #0d6efd; background: rgba(13, 110, 253, 0.03); }
        .show-side-header { padding: .25rem .5rem .5rem .5rem; }
        .show-side-header .side-badge { font-size: .9rem; }
      </style>
    </div>
{/block}
