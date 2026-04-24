{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">貸借対照表</h1>
      <p class="text-muted mb-0 small">
        対象日: <code>{$to_date|escape}</code> 時点
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm"
         href="/ui/bs?format=pdf{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
        <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/bs" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="bs-year" class="form-label small text-muted mb-1">年</label>
        <input id="bs-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="bs-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="bs-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/bs" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if $has_jgaap}
    <div class="row g-3">
      <div class="col-lg-6">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <h2 class="h5 mb-3">資産の部</h2>
          <table class="table table-sm align-middle mb-0">
            <tbody>
              {foreach $bs_order.assetGroups as $grp}
                {if isset($bs[$grp.code])}
                  <tr class="table-secondary fw-semibold">
                    <td>{$grp.label|escape}</td>
                    <td class="text-end">{$bs[$grp.code].subtotal|escape}</td>
                  </tr>
                  {foreach $bs[$grp.code].lines as $line}
                    <tr>
                      <td class="ps-4 small text-muted">{$line.label|escape}</td>
                      <td class="text-end">{$line.amount|escape}</td>
                    </tr>
                  {/foreach}
                {/if}
              {/foreach}
              {if isset($bs.asset_total)}
                <tr class="table-primary fw-bold">
                  <td>資産合計</td>
                  <td class="text-end">{$bs.asset_total.subtotal|escape}</td>
                </tr>
              {/if}
            </tbody>
          </table>
        </section>
      </div>
      <div class="col-lg-6">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <h2 class="h5 mb-3">負債の部</h2>
          <table class="table table-sm align-middle mb-3">
            <tbody>
              {foreach $bs_order.liabilityGroups as $grp}
                {if isset($bs[$grp.code])}
                  <tr class="table-secondary fw-semibold">
                    <td>{$grp.label|escape}</td>
                    <td class="text-end">{$bs[$grp.code].subtotal|escape}</td>
                  </tr>
                  {foreach $bs[$grp.code].lines as $line}
                    <tr>
                      <td class="ps-4 small text-muted">{$line.label|escape}</td>
                      <td class="text-end">{$line.amount|escape}</td>
                    </tr>
                  {/foreach}
                {/if}
              {/foreach}
              {if isset($bs.liability_total)}
                <tr class="table-primary fw-bold">
                  <td>負債合計</td>
                  <td class="text-end">{$bs.liability_total.subtotal|escape}</td>
                </tr>
              {/if}
            </tbody>
          </table>

          <h2 class="h5 mb-3">純資産の部</h2>
          <table class="table table-sm align-middle mb-0">
            <tbody>
              {foreach $bs_order.equityGroups as $grp}
                {if isset($bs[$grp.code])}
                  <tr class="table-secondary fw-semibold">
                    <td>{$grp.label|escape}</td>
                    <td class="text-end">{$bs[$grp.code].subtotal|escape}</td>
                  </tr>
                  {foreach $bs[$grp.code].lines as $line}
                    <tr>
                      <td class="ps-4 small text-muted">{$line.label|escape}</td>
                      <td class="text-end">{$line.amount|escape}</td>
                    </tr>
                  {/foreach}
                {/if}
              {/foreach}
              {if isset($bs.equity_total)}
                <tr class="table-primary fw-bold">
                  <td>純資産合計</td>
                  <td class="text-end">{$bs.equity_total.subtotal|escape}</td>
                </tr>
              {/if}
            </tbody>
          </table>
        </section>
      </div>
    </div>
  {else}
    {* Simplified (category-derived) fallback. *}
    <section class="rucaro-card p-3 shadow-sm">
      <p class="small text-muted mb-2">J-GAAP 階層が未設定のため、簡易表示で出力します。</p>
      <div class="row g-3">
        <div class="col-lg-6">
          <h2 class="h5 mb-3">資産の部</h2>
          <table class="table table-sm align-middle mb-0">
            <tbody>
              {foreach $bs as $code => $section}
                {if $code == 'assets' || $code == 'asset'}
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
                {/if}
              {/foreach}
            </tbody>
          </table>
        </div>
        <div class="col-lg-6">
          <h2 class="h5 mb-3">負債の部 ／ 純資産の部</h2>
          <table class="table table-sm align-middle mb-0">
            <tbody>
              {foreach $bs as $code => $section}
                {if $code == 'liabilities' || $code == 'liability' || $code == 'equity'}
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
                {/if}
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>
    </section>
  {/if}
{/block}
