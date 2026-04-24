{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">損益分岐点分析</h1>
      <p class="text-muted mb-0 small">
        対象期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      {if $has_analysis}
        <a class="btn btn-outline-primary btn-sm"
           href="/ui/bep?format=pdf{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
          <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
        </a>
      {/if}
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/bep" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="bep-year" class="form-label small text-muted mb-1">年</label>
        <input id="bep-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-3">
        <label for="bep-month" class="form-label small text-muted mb-1">月 (1-12、任意)</label>
        <input id="bep-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/bep" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if $error_message !== ''}
    <div class="alert alert-warning small mb-4">
      <i class="bi bi-exclamation-triangle"></i> {$error_message|escape}
    </div>
  {/if}

  {if $has_analysis}
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <div class="small text-muted">売上高</div>
          <div class="fs-4 fw-bold">{$bep.sales|escape}</div>
        </section>
      </div>
      <div class="col-md-4">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <div class="small text-muted">損益分岐点売上高</div>
          <div class="fs-4 fw-bold">{$bep.bepSales|escape}</div>
          <div class="small text-muted">（達成率 {$bep.bepRatio|escape}）</div>
        </section>
      </div>
      <div class="col-md-4">
        <section class="rucaro-card p-3 shadow-sm h-100 {if $bep.belowBep}border border-danger{/if}">
          <div class="small text-muted">安全余裕率</div>
          <div class="fs-4 fw-bold {if $bep.belowBep}text-danger{/if}">{$bep.safetyMarginRatio|escape}</div>
          {if $bep.belowBep}<div class="small text-danger">損益分岐点を下回っています</div>{/if}
        </section>
      </div>
    </div>

    <section class="rucaro-card p-3 shadow-sm mb-4">
      <h2 class="h5 mb-3">CVP サマリ</h2>
      <table class="table table-sm mb-0">
        <tbody>
          <tr><th>売上高</th><td class="text-end">{$bep.sales|escape}</td></tr>
          <tr><th>変動費</th><td class="text-end">{$bep.variableCosts|escape}</td></tr>
          <tr class="table-secondary"><th>限界利益</th><td class="text-end fw-semibold">{$bep.contributionMargin|escape}</td></tr>
          <tr><th>限界利益率</th><td class="text-end">{$bep.contributionMarginRate|escape}</td></tr>
          <tr><th>固定費</th><td class="text-end">{$bep.fixedCosts|escape}</td></tr>
          <tr class="table-primary"><th>営業利益</th><td class="text-end fw-bold">{$bep.operatingProfit|escape}</td></tr>
          <tr><th>損益分岐点売上高</th><td class="text-end">{$bep.bepSales|escape}</td></tr>
          <tr><th>損益分岐点比率</th><td class="text-end">{$bep.bepRatio|escape}</td></tr>
          <tr><th>安全余裕率</th><td class="text-end">{$bep.safetyMarginRatio|escape}</td></tr>
        </tbody>
      </table>
    </section>

    <div class="row g-3">
      <div class="col-lg-4">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <h2 class="h6 mb-3">売上内訳</h2>
          <table class="table table-sm mb-0">
            <tbody>
              {foreach $bep.salesBreakdown as $r}
                <tr>
                  <td class="small"><code>{$r.code|escape}</code> {$r.name|escape}</td>
                  <td class="text-end small">{$r.amount|escape}</td>
                </tr>
              {foreachelse}
                <tr><td colspan="2" class="text-muted small text-center">データなし</td></tr>
              {/foreach}
            </tbody>
          </table>
        </section>
      </div>
      <div class="col-lg-4">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <h2 class="h6 mb-3">変動費内訳</h2>
          <table class="table table-sm mb-0">
            <tbody>
              {foreach $bep.variableBreakdown as $r}
                <tr>
                  <td class="small"><code>{$r.code|escape}</code> {$r.name|escape}</td>
                  <td class="text-end small">{$r.amount|escape}</td>
                </tr>
              {foreachelse}
                <tr><td colspan="2" class="text-muted small text-center">データなし</td></tr>
              {/foreach}
            </tbody>
          </table>
        </section>
      </div>
      <div class="col-lg-4">
        <section class="rucaro-card p-3 shadow-sm h-100">
          <h2 class="h6 mb-3">固定費内訳</h2>
          <table class="table table-sm mb-0">
            <tbody>
              {foreach $bep.fixedBreakdown as $r}
                <tr>
                  <td class="small"><code>{$r.code|escape}</code> {$r.name|escape}</td>
                  <td class="text-end small">{$r.amount|escape}</td>
                </tr>
              {foreachelse}
                <tr><td colspan="2" class="text-muted small text-center">データなし</td></tr>
              {/foreach}
            </tbody>
          </table>
        </section>
      </div>
    </div>
  {else}
    <section class="rucaro-card p-4 shadow-sm text-center text-muted">
      <i class="bi bi-info-circle"></i>
      損益分岐点分析のデータがありません。CVP 分類の設定を確認してください。
    </section>
  {/if}
{/block}
