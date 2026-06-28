{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">合計残高試算表</h1>
      <p class="text-muted mb-0 small">
        期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/trial-balance" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="tb-year" class="form-label small text-muted mb-1">年</label>
        <input id="tb-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="tb-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="tb-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/trial-balance" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if count($rows) == 0}
    <div class="alert alert-info">対象期間に仕訳がありません。</div>
  {else}
    <section class="rucaro-card p-3 shadow-sm">
      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead>
            <tr>
              <th scope="col" style="width:90px;">コード</th>
              <th scope="col">科目</th>
              <th scope="col" style="width:80px;">区分</th>
              <th scope="col" class="text-end" style="width:140px;">期首残高</th>
              <th scope="col" class="text-end" style="width:140px;">借方</th>
              <th scope="col" class="text-end" style="width:140px;">貸方</th>
              <th scope="col" class="text-end" style="width:140px;">残高</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $r}
              <tr>
                <td><code>{$r.accountTitleCode|escape}</code></td>
                <td>{$r.accountTitleName|escape}</td>
                <td><span class="badge text-bg-light text-muted">{$r.accountCategoryLabel|escape}</span></td>
                <td class="text-end">{$r.openingBalance|escape}</td>
                <td class="text-end">{$r.debitTotal|escape}</td>
                <td class="text-end">{$r.creditTotal|escape}</td>
                <td class="text-end fw-semibold">{$r.balance|escape}</td>
              </tr>
            {/foreach}
          </tbody>
          <tfoot>
            <tr class="fw-semibold table-secondary">
              <td colspan="4" class="text-end">合計</td>
              <td class="text-end">{$totals.debit|escape}</td>
              <td class="text-end">{$totals.credit|escape}</td>
              <td class="text-end">—</td>
            </tr>
            <tr class="fw-semibold {if $totals.balanced}table-success{else}table-danger{/if}">
              <td colspan="7" class="text-end">
                {if $totals.balanced}
                  <i class="bi bi-check-circle"></i> 借方合計 = 貸方合計（一致）
                {else}
                  <i class="bi bi-exclamation-triangle"></i> 借方合計 ≠ 貸方合計（不一致）
                {/if}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>
  {/if}
{/block}
