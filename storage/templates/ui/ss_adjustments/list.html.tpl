{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">純資産変動調整</h1>
      <p class="text-muted mb-0">株主資本等変動計算書 (S/S) の手動調整行を一覧・編集します。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/ss-adjustments/new">
        <i class="bi bi-plus-lg"></i> 新規調整
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/ss-adjustments" class="row g-2 align-items-end">
      <div class="col-md-8">
        <label class="form-label small text-muted mb-1">会計期間</label>
        <select name="fiscalTermId" class="form-select form-select-sm">
          <option value="">（選択してください）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $filter_fiscal_term == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i> 表示</button>
      </div>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>項目</th>
            <th>列</th>
            <th>変動事由</th>
            <th class="text-end">金額</th>
            <th class="text-end">並び順</th>
            <th>メモ</th>
            <th class="text-end" style="min-width: 100px;">操作</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="7" class="text-center text-muted py-5">
                該当する純資産変動調整はありません。
              </td>
            </tr>
          {else}
            {foreach $items as $a}
              <tr>
                <td>{$a.label|escape}</td>
                <td><small>{$a.sectionLabel|escape}</small></td>
                <td><small>{$a.changeLabel|escape}</small></td>
                <td class="text-end">{$a.amount|escape}</td>
                <td class="text-end">{$a.sortOrder}</td>
                <td><small class="text-muted">{$a.notes|escape}</small></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/ss-adjustments/{$a.id|escape}">編集</a>
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
