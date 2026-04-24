{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">仕訳一覧</h1>
      <p class="text-muted mb-0">発生日・摘要・状態で絞り込み、クリックで詳細を開けます。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/journals/new">
        <i class="bi bi-plus-lg"></i> 新規仕訳
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/journals" class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="form-label small text-muted mb-1">年</label>
        <select name="year" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $year_options as $y}
            <option value="{$y}"{if $filter_year == $y} selected{/if}>{$y}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted mb-1">月</label>
        <select name="month" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {section name=m start=1 loop=13 step=1}
            <option value="{$smarty.section.m.index}"{if $filter_month == $smarty.section.m.index} selected{/if}>{$smarty.section.m.index} 月</option>
          {/section}
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted mb-1">勘定科目</label>
        <select name="accountTitleId" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $account_titles as $a}
            <option value="{$a.id|escape}"{if $filter_account == $a.id} selected{/if}>{$a.code|escape} — {$a.name|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted mb-1">ステータス</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $status_options as $s}
            <option value="{$s|escape}"{if $filter_status == $s} selected{/if}>{$s|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted mb-1">摘要キーワード</label>
        <input type="search" name="q" value="{$filter_q|escape}" class="form-control form-control-sm" placeholder="例: 請求書">
      </div>
      <div class="col-md-12 d-flex gap-2 justify-content-end">
        <input type="hidden" name="sortBy" value="{$sort_by|escape}">
        <input type="hidden" name="sortOrder" value="{$sort_order|escape}">
        <select name="pageSize" class="form-select form-select-sm" style="width: auto">
          {foreach $page_sizes as $ps}
            <option value="{$ps}"{if $page_size == $ps} selected{/if}>{$ps} 件 / ページ</option>
          {/foreach}
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/journals" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-x-circle"></i> クリア
        </a>
      </div>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            {assign var="cols" value=[
              ['id' => 'journal_date', 'label' => '発生日'],
              ['id' => 'summary',      'label' => '摘要'],
              ['id' => 'total_amount', 'label' => '合計金額'],
              ['id' => 'created_by',   'label' => '起票者'],
              ['id' => 'status',       'label' => 'ステータス']
            ]}
            {foreach $cols as $col}
              {assign var="next_order" value="asc"}
              {if $sort_by == $col.id && $sort_order == "asc"}{assign var="next_order" value="desc"}{/if}
              {capture name="link"}?sortBy={$col.id}&sortOrder={$next_order}&{$query_string_base}&page={$page}{/capture}
              <th scope="col"{if $col.id == 'total_amount'} class="text-end"{/if}>
                <a class="text-decoration-none text-reset" href="/ui/journals{$smarty.capture.link}">
                  {$col.label|escape}
                  {if $sort_by == $col.id}
                    {if $sort_order == "asc"}<i class="bi bi-caret-up-fill text-primary"></i>{else}<i class="bi bi-caret-down-fill text-primary"></i>{/if}
                  {/if}
                </a>
              </th>
            {/foreach}
            <th scope="col" class="text-end" style="min-width: 160px">アクション</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="6" class="text-center text-muted py-5">
                条件に合致する仕訳はありません。<br>
                <a class="btn btn-sm btn-outline-primary mt-3" href="/ui/journals/new">
                  <i class="bi bi-plus-lg"></i> 新規仕訳を作成する
                </a>
              </td>
            </tr>
          {else}
            {foreach $items as $j}
              <tr>
                <td><code>{$j.journalDate|escape}</code></td>
                <td>
                  <a class="text-decoration-none" href="/ui/journals/{$j.id|escape}">
                    {$j.summary|default:'（摘要なし）'|escape}
                  </a>
                </td>
                <td class="text-end">{$j.totalAmount|escape}</td>
                <td class="text-muted small"><code>{$j.createdBy|truncate:8:""|escape}…</code></td>
                <td>
                  {assign var="badge" value="text-bg-secondary"}
                  {if $j.status == 'draft'}{assign var="badge" value="text-bg-warning"}{/if}
                  {if $j.status == 'posted'}{assign var="badge" value="text-bg-success"}{/if}
                  {if $j.status == 'approved'}{assign var="badge" value="text-bg-info"}{/if}
                  {if $j.status == 'rejected'}{assign var="badge" value="text-bg-danger"}{/if}
                  <span class="badge {$badge}">{$j.status|escape}</span>
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/journals/{$j.id|escape}">詳細</a>
                  {if $j.status == 'draft'}
                    <a class="btn btn-sm btn-outline-danger" href="/ui/journals/{$j.id|escape}/delete">削除</a>
                  {/if}
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>

    {if $total_pages > 1}
      <nav aria-label="journal pagination" class="p-3 border-top">
        <ul class="pagination pagination-sm mb-0 justify-content-end">
          {assign var="prev_page" value=$page-1}
          {assign var="next_page" value=$page+1}
          <li class="page-item{if $page <= 1} disabled{/if}">
            <a class="page-link" href="/ui/journals?{$query_string_base}&page={$prev_page}">前へ</a>
          </li>
          {section name=p loop=$total_pages start=0 step=1}
            {assign var="p" value=$smarty.section.p.index+1}
            {if $p == 1 || $p == $total_pages || ($p >= $page-2 && $p <= $page+2)}
              <li class="page-item{if $p == $page} active{/if}">
                <a class="page-link" href="/ui/journals?{$query_string_base}&page={$p}">{$p}</a>
              </li>
            {elseif $p == $page-3 || $p == $page+3}
              <li class="page-item disabled"><span class="page-link">…</span></li>
            {/if}
          {/section}
          <li class="page-item{if $page >= $total_pages} disabled{/if}">
            <a class="page-link" href="/ui/journals?{$query_string_base}&page={$next_page}">次へ</a>
          </li>
        </ul>
        <div class="text-end small text-muted mt-2">全 {$total} 件 / {$page} / {$total_pages} ページ</div>
      </nav>
    {else}
      <div class="p-3 text-end small text-muted border-top">全 {$total} 件</div>
    {/if}
  </section>
{/block}
