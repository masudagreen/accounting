{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">予算</h1>
      <p class="text-muted mb-0">会計期間ごとの予算ヘッダを一覧します。クリックで詳細・承認・ロック操作へ遷移します。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/budgets/new">
        <i class="bi bi-plus-lg"></i> 新規予算
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/budgets" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label small text-muted mb-1">会計期間</label>
        <select name="fiscalTermId" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $filter_fiscal_term == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted mb-1">ステータス</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $status_options as $s}
            <option value="{$s|escape}"{if $filter_status == $s} selected{/if}>{$s|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i> 絞り込み</button>
        <a href="/ui/budgets" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> クリア</a>
      </div>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>予算名</th>
            <th>ステータス</th>
            <th class="text-end">行数</th>
            <th class="text-end">年間合計</th>
            <th>更新日時</th>
            <th class="text-end" style="min-width: 160px;">操作</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="6" class="text-center text-muted py-5">
                該当する予算はありません。<br>
                <a class="btn btn-sm btn-outline-primary mt-3" href="/ui/budgets/new">
                  <i class="bi bi-plus-lg"></i> 新規予算を作成
                </a>
              </td>
            </tr>
          {else}
            {foreach $items as $b}
              <tr>
                <td><a class="text-decoration-none" href="/ui/budgets/{$b.id|escape}">{$b.name|escape}</a></td>
                <td>
                  {assign var="badge" value="text-bg-secondary"}
                  {if $b.status == 'draft'}{assign var="badge" value="text-bg-warning"}{/if}
                  {if $b.status == 'approved'}{assign var="badge" value="text-bg-info"}{/if}
                  {if $b.status == 'locked'}{assign var="badge" value="text-bg-success"}{/if}
                  <span class="badge {$badge}">{$b.status|escape}</span>
                </td>
                <td class="text-end">{$b.lineCount}</td>
                <td class="text-end">{$b.annualTotal|escape}</td>
                <td class="small text-muted">{$b.updatedAt|escape}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/budgets/{$b.id|escape}">詳細</a>
                  <a class="btn btn-sm btn-outline-primary" href="/ui/budgets/{$b.id|escape}/variance">予実</a>
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
    <div class="p-3 text-end small text-muted border-top">全 {$total} 件</div>
  </section>
{/block}
