{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">資金繰り計画</h1>
      <p class="text-muted mb-0">会計期間ごとの資金繰り (キャッシュプラン) を一覧します。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/cash-plans/new">
        <i class="bi bi-plus-lg"></i> 新規資金繰り計画
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/cash-plans" class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label small text-muted mb-1">会計期間</label>
        <select name="fiscalTermId" class="form-select form-select-sm">
          <option value="">（すべて）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $filter_fiscal_term == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-6 text-end">
        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i> 絞り込み</button>
        <a href="/ui/cash-plans" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> クリア</a>
      </div>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>計画名</th>
            <th class="text-end">期首残高</th>
            <th class="text-end">期末残高 (12月末)</th>
            <th class="text-end">通貨</th>
            <th class="text-end">明細数</th>
            <th>更新日時</th>
            <th class="text-end" style="min-width: 100px;">操作</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="7" class="text-center text-muted py-5">
                登録された資金繰り計画はありません。<br>
                <a class="btn btn-sm btn-outline-primary mt-3" href="/ui/cash-plans/new">
                  <i class="bi bi-plus-lg"></i> 新規資金繰り計画を作成
                </a>
              </td>
            </tr>
          {else}
            {foreach $items as $p}
              <tr>
                <td><a class="text-decoration-none" href="/ui/cash-plans/{$p.id|escape}">{$p.name|escape}</a></td>
                <td class="text-end">{$p.openingBalance|escape}</td>
                <td class="text-end"><strong>{$p.closingBalance|escape}</strong></td>
                <td class="text-end"><small>{$p.currency|escape}</small></td>
                <td class="text-end">{$p.entryCount}</td>
                <td class="small text-muted">{$p.updatedAt|escape}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/cash-plans/{$p.id|escape}">詳細</a>
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
