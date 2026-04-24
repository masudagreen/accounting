{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">損益計算書</h1>
      <p class="text-muted mb-0 small">
        対象期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm"
         href="/ui/pl?format=pdf{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
        <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/pl" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="pl-year" class="form-label small text-muted mb-1">年</label>
        <input id="pl-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="pl-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="pl-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/pl" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  <section class="rucaro-card p-3 shadow-sm">
    {if $has_jgaap}
      <table class="table table-sm align-middle mb-0">
        <thead>
          <tr>
            <th scope="col" style="width:70%;">項目</th>
            <th scope="col" class="text-end" style="width:30%;">金額</th>
          </tr>
        </thead>
        <tbody>
          {foreach $pl_order as $row}
            {if isset($pl[$row.code])}
              {if $row.isSubtotal || $row.isTotal}
                <tr class="{if $row.isTotal}table-primary fw-bold{else}table-secondary fw-semibold{/if}">
                  <td>{$row.label|escape}</td>
                  <td class="text-end">{$pl[$row.code].subtotal|escape}</td>
                </tr>
              {else}
                <tr class="table-light fw-semibold">
                  <td>{$row.label|escape}</td>
                  <td class="text-end">{$pl[$row.code].subtotal|escape}</td>
                </tr>
                {foreach $pl[$row.code].lines as $line}
                  <tr>
                    <td class="ps-4 small text-muted">{$line.label|escape}</td>
                    <td class="text-end">{$line.amount|escape}</td>
                  </tr>
                {/foreach}
              {/if}
            {/if}
          {/foreach}
        </tbody>
      </table>
    {else}
      {* Simplified (category-derived) fallback. *}
      <p class="small text-muted mb-2">J-GAAP 段階表示が未設定のため、簡易表示で出力します。</p>
      <table class="table table-sm align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">項目</th>
            <th scope="col" class="text-end">金額</th>
          </tr>
        </thead>
        <tbody>
          {foreach $pl as $code => $section}
            <tr class="table-secondary fw-semibold">
              <td>{$section.label|escape}</td>
              <td class="text-end">{$section.subtotal|escape}</td>
            </tr>
            {foreach $section.lines as $line}
              <tr>
                <td class="ps-4 small text-muted">{$line.label|escape}</td>
                <td class="text-end">{$line.amount|escape}</td>
              </tr>
            {/foreach}
          {/foreach}
          {if isset($totals.net_income)}
            <tr class="table-primary fw-bold">
              <td>当期純利益</td>
              <td class="text-end">{$totals.net_income|escape}</td>
            </tr>
          {/if}
        </tbody>
      </table>
    {/if}
  </section>
{/block}
