{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">予実対比: {$budget.name|escape}</h1>
      <p class="text-muted mb-0">期首〜基準日までの実績と予算 (12ヶ月割) を勘定科目別に比較します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/budgets/{$budget.id|escape}">
        <i class="bi bi-arrow-left"></i> 詳細へ戻る
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/budgets/{$budget.id|escape}/variance" class="d-flex align-items-end gap-2">
      <div>
        <label class="form-label small text-muted mb-1">基準日</label>
        <input type="date" name="asOf" value="{$as_of|escape}" class="form-control form-control-sm">
      </div>
      <div class="small text-muted">期間開始: <code>{$period_from|escape}</code></div>
      <button type="submit" class="btn btn-sm btn-outline-primary ms-auto"><i class="bi bi-arrow-repeat"></i> 更新</button>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>勘定科目コード</th>
            <th>勘定科目名</th>
            <th class="text-end">予算</th>
            <th class="text-end">実績</th>
            <th class="text-end">差異</th>
            <th class="text-end">消化率 %</th>
          </tr>
        </thead>
        <tbody>
          {foreach $rows as $r}
            <tr>
              <td><code>{$r.accountTitleCode|escape}</code></td>
              <td>{$r.accountTitleName|escape}</td>
              <td class="text-end">{$r.budgetAmount|escape}</td>
              <td class="text-end">{$r.actualAmount|escape}</td>
              <td class="text-end">{$r.varianceAmount|escape}</td>
              <td class="text-end">{if $r.usageRate != ''}{$r.usageRate|escape}{else}—{/if}</td>
            </tr>
          {foreachelse}
            <tr><td colspan="6" class="text-center text-muted py-4">データがありません。</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </section>
{/block}
