{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">注記表</h1>
      <p class="text-muted mb-0 small">
        会計期 <code>{$selected_fiscal_term|escape}</code> の注記一覧（{$total_count|escape} 件）
      </p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm"
         href="/ui/notes?format=pdf&fiscalTermId={$selected_fiscal_term|escape}">
        <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/notes" class="row g-2 align-items-end">
      <div class="col-md-6">
        <label for="notes-term" class="form-label small text-muted mb-1">会計期 ID</label>
        <input id="notes-term" name="fiscalTermId" class="form-control form-control-sm"
               value="{$selected_fiscal_term|escape}" placeholder="01HW...">
      </div>
      <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 表示
        </button>
      </div>
    </form>
  </section>

  {if $total_count == 0}
    <section class="rucaro-card p-4 shadow-sm text-center text-muted">
      <i class="bi bi-info-circle"></i>
      この会計期の注記表エントリは登録されていません。<br>
      <small>テンプレートからの一括インポート機能は API 側 ( /api/v1/fs-notes/bulk-import ) を利用してください。</small>
    </section>
  {else}
    {foreach $categories as $group}
      <section class="rucaro-card p-3 shadow-sm mb-4">
        <h2 class="h5 mb-3">
          {$group.label|escape}
          <span class="badge text-bg-secondary">{count($group.notes)}</span>
        </h2>
        <div class="list-group list-group-flush">
          {foreach $group.notes as $note}
            <div class="list-group-item px-0">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h3 class="h6 mb-0">
                  {$note.label|escape}
                  {if !$note.isActive}<span class="badge text-bg-secondary ms-1">非表示</span>{/if}
                </h3>
                <small class="text-muted">
                  {if $note.templateCode !== ''}template: <code>{$note.templateCode|escape}</code>{/if}
                </small>
              </div>
              <div class="small text-body">{$note.body|escape|nl2br}</div>
            </div>
          {/foreach}
        </div>
      </section>
    {/foreach}
  {/if}
{/block}
