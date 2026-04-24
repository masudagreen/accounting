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
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="/ui/journals">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
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

  {if $can_edit}
    {include file="journals/form.html.tpl"}
  {else}
    <div class="rucaro-card p-4 shadow-sm">
      <dl class="row g-2 mb-4">
        <dt class="col-sm-2 text-muted">発生日</dt>
        <dd class="col-sm-4"><code>{$form_journal.journalDate|escape}</code></dd>
        <dt class="col-sm-2 text-muted">合計金額</dt>
        <dd class="col-sm-4">{if isset($form_journal.totalAmount)}{$form_journal.totalAmount|escape}{/if}</dd>
        <dt class="col-sm-2 text-muted">摘要</dt>
        <dd class="col-sm-10">{$form_journal.summary|default:'（摘要なし）'|escape}</dd>
        <dt class="col-sm-2 text-muted">起票者</dt>
        <dd class="col-sm-4"><code>{if isset($form_journal.createdBy)}{$form_journal.createdBy|escape}{/if}</code></dd>
        <dt class="col-sm-2 text-muted">作成日時</dt>
        <dd class="col-sm-4">{if isset($form_journal.createdAt)}{$form_journal.createdAt|escape}{/if}</dd>
      </dl>
      <h2 class="h6 mb-2">明細行</h2>
      <div class="table-responsive">
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
            {foreach $form_lines as $line}
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
    </div>
  {/if}
{/block}
