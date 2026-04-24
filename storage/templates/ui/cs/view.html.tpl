{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">キャッシュフロー計算書</h1>
      <p class="text-muted mb-0 small">
        対象期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm"
         href="/ui/cs?format=pdf{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
        <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/cs" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="cs-year" class="form-label small text-muted mb-1">年</label>
        <input id="cs-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="cs-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="cs-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/cs" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if $has_cs}
    <section class="rucaro-card p-3 shadow-sm">
      <h2 class="h5 mb-3">間接法 キャッシュフロー計算書</h2>
      <table class="table table-sm align-middle mb-0">
        <thead>
          <tr class="table-light">
            <th style="width:72%;">項目</th>
            <th class="text-end" style="width:28%;">金額</th>
          </tr>
        </thead>
        <tbody>
          {foreach $cs_order as $row}
            {if isset($cs[$row.code])}
              {if $row.isTotal}
                <tr class="table-primary fw-bold">
                  <td class="ps-{if $row.indent > 0}4{else}2{/if}">{$row.label|escape}</td>
                  <td class="text-end">{$cs[$row.code].subtotal|escape}</td>
                </tr>
              {elseif $row.isSubtotal}
                <tr class="table-secondary fw-semibold">
                  <td class="ps-{if $row.indent > 0}4{else}2{/if}">{$row.label|escape}</td>
                  <td class="text-end">{$cs[$row.code].subtotal|escape}</td>
                </tr>
              {else}
                <tr>
                  <td class="ps-{if $row.indent > 0}4{else}2{/if} fw-semibold">{$row.label|escape}</td>
                  <td class="text-end">{$cs[$row.code].subtotal|escape}</td>
                </tr>
                {foreach $cs[$row.code].lines as $line}
                  <tr>
                    <td class="ps-5 small text-muted">{$line.label|escape}</td>
                    <td class="text-end small text-muted">{$line.amount|escape}</td>
                  </tr>
                {/foreach}
              {/if}
            {/if}
          {/foreach}
        </tbody>
      </table>
    </section>
  {else}
    <section class="rucaro-card p-4 shadow-sm text-center text-muted">
      <i class="bi bi-info-circle"></i>
      キャッシュフロー計算書のデータがありません。<br>
      会計期・勘定科目のマッピングが完了しているかご確認ください。
    </section>
  {/if}
{/block}
