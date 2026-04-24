{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">株主資本等変動計算書</h1>
      <p class="text-muted mb-0 small">
        対象期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      {if $has_ss}
        <a class="btn btn-outline-primary btn-sm"
           href="/ui/ss?format=pdf{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
          <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
        </a>
      {/if}
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/ss" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="ss-year" class="form-label small text-muted mb-1">年</label>
        <input id="ss-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="ss-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="ss-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/ss" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if $error_message !== ''}
    <div class="alert alert-warning small mb-4">
      <i class="bi bi-exclamation-triangle"></i> {$error_message|escape}
    </div>
  {/if}

  {if $has_ss}
    <section class="rucaro-card p-3 shadow-sm">
      <h2 class="h5 mb-3">株主資本等変動計算書（格子表示）</h2>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr class="table-light">
              <th>項目</th>
              {foreach $ss.columns as $col}
                <th class="text-end">{$col.label|escape}</th>
              {/foreach}
              <th class="text-end">合計</th>
            </tr>
          </thead>
          <tbody>
            <tr class="table-secondary fw-semibold">
              <td>期首残高</td>
              {foreach $ss.columns as $col}
                <td class="text-end">{if isset($ss.opening[$col.code])}{$ss.opening[$col.code]|escape}{else}0{/if}</td>
              {/foreach}
              <td class="text-end">{$ss.totals.opening|escape}</td>
            </tr>
            {foreach $ss.rows as $row}
              <tr>
                <td class="ps-3 small">{$row.label|escape}</td>
                {foreach $ss.columns as $col}
                  <td class="text-end small">{if isset($row.amounts[$col.code])}{$row.amounts[$col.code]|escape}{else}0{/if}</td>
                {/foreach}
                <td class="text-end small text-muted">—</td>
              </tr>
            {foreachelse}
              <tr>
                <td colspan="{count($ss.columns)+2}" class="text-muted small text-center">
                  当期変動額の記録はありません
                </td>
              </tr>
            {/foreach}
            <tr class="table-secondary fw-semibold">
              <td>当期変動額合計</td>
              {foreach $ss.columns as $col}
                <td class="text-end">—</td>
              {/foreach}
              <td class="text-end">{$ss.totals.totalChange|escape}</td>
            </tr>
            <tr class="table-primary fw-bold">
              <td>期末残高</td>
              {foreach $ss.columns as $col}
                <td class="text-end">{if isset($ss.ending[$col.code])}{$ss.ending[$col.code]|escape}{else}0{/if}</td>
              {/foreach}
              <td class="text-end">{$ss.totals.ending|escape}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  {else}
    <section class="rucaro-card p-4 shadow-sm text-center text-muted">
      <i class="bi bi-info-circle"></i>
      株主資本等変動計算書のデータがありません。
    </section>
  {/if}
{/block}
