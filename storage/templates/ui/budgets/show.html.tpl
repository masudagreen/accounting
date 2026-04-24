{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">{$budget.name|escape}</h1>
      <p class="text-muted mb-0">予算の詳細（ステータス: <strong>{$budget.status|escape}</strong>）</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary btn-sm" href="/ui/budgets/{$budget.id|escape}/variance">
        <i class="bi bi-graph-up"></i> 予実対比
      </a>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/budgets">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
    </div>
  </header>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <dl class="row g-2 mb-0">
      <dt class="col-sm-2 text-muted">予算 ID</dt>
      <dd class="col-sm-4"><code>{$budget.id|escape}</code></dd>
      <dt class="col-sm-2 text-muted">年間合計</dt>
      <dd class="col-sm-4 text-end">{$budget.annualTotal|escape}</dd>
      <dt class="col-sm-2 text-muted">メモ</dt>
      <dd class="col-sm-10">{$budget.notes|default:'（なし）'|escape}</dd>
      <dt class="col-sm-2 text-muted">承認日時</dt>
      <dd class="col-sm-4">{$budget.approvedAt|default:'—'|escape}</dd>
      <dt class="col-sm-2 text-muted">更新日時</dt>
      <dd class="col-sm-4">{$budget.updatedAt|escape}</dd>
    </dl>
  </section>

  <section class="rucaro-card shadow-sm mb-4">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>勘定科目</th>
            {section name=m start=1 loop=13 step=1}<th class="text-end">{$smarty.section.m.index}月</th>{/section}
            <th class="text-end">年計</th>
            <th>メモ</th>
          </tr>
        </thead>
        <tbody>
          {foreach $budget.lines as $li}
            <tr>
              <td><code class="small">{$li.accountTitleId|truncate:10:""|escape}…</code></td>
              {section name=m start=0 loop=12 step=1}<td class="text-end">{$li.monthly[$smarty.section.m.index]|escape}</td>{/section}
              <td class="text-end"><strong>{$li.total|escape}</strong></td>
              <td><small>{$li.memo|escape}</small></td>
            </tr>
          {foreachelse}
            <tr><td colspan="15" class="text-center text-muted py-4">明細行はありません。</td></tr>
          {/foreach}
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th>月計</th>
            {foreach $monthly_totals as $mt}<th class="text-end">{$mt|escape}</th>{/foreach}
            <th class="text-end">{$budget.annualTotal|escape}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </section>

  <section class="rucaro-card p-4 shadow-sm">
    <h2 class="h5 mb-3">ライフサイクル操作</h2>
    <div class="d-flex gap-2 flex-wrap">
      {if $can_approve}
        <form method="post" action="/ui/budgets/{$budget.id|escape}/approve">
          <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
          <button type="submit" class="btn btn-outline-success"><i class="bi bi-check2"></i> 承認</button>
        </form>
      {/if}
      {if $can_lock}
        <form method="post" action="/ui/budgets/{$budget.id|escape}/lock">
          <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
          <button type="submit" class="btn btn-outline-primary"><i class="bi bi-lock"></i> ロック</button>
        </form>
      {/if}
      {if $can_edit}
        <form method="post" action="/ui/budgets/{$budget.id|escape}/delete"
              onsubmit="return confirm('この予算を削除しますか？この操作は取り消せません。');">
          <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
          <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> 削除</button>
        </form>
      {/if}
      {if !$can_edit && !$can_approve && !$can_lock}
        <p class="text-muted mb-0">ロック済みのため編集操作はできません。</p>
      {/if}
    </div>
  </section>
{/block}
