{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">青色申告決算書</h1>
      <p class="text-muted mb-0 small">
        個人事業主向け・全 4 ページ（損益計算書／月別売上／減価償却ほか／貸借対照表）
      </p>
    </div>
    <div>
      {if $has_form}
        <a class="btn btn-outline-primary btn-sm"
           href="/ui/blue-return?format=pdf&fiscalTermId={$selected_fiscal_term|escape}">
          <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
        </a>
      {/if}
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/blue-return" class="row g-2 align-items-end">
      <div class="col-md-6">
        <label for="br-term" class="form-label small text-muted mb-1">会計期 ID</label>
        <input id="br-term" name="fiscalTermId" class="form-control form-control-sm"
               value="{$selected_fiscal_term|escape}" placeholder="01HW...">
      </div>
      <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 表示
        </button>
      </div>
    </form>
  </section>

  {if !$has_form}
    <section class="rucaro-card p-4 shadow-sm">
      <div class="text-center text-muted">
        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
        この会計期の青色申告決算書はまだ作成されていません。<br>
        <small>法人の会計単位では青色申告決算書は不要です。個人事業主のみ対象となります。</small>
      </div>
    </section>
  {else}
    <section class="rucaro-card p-3 shadow-sm mb-4">
      <div class="row g-3 small">
        <div class="col-md-3">
          <div class="text-muted">Form ID</div>
          <div class="fw-semibold"><code>{$form.id|escape}</code></div>
        </div>
        <div class="col-md-3">
          <div class="text-muted">様式</div>
          <div class="fw-semibold">
            {if $form.formType == 'general'}一般用
            {elseif $form.formType == 'agricultural'}農業所得用
            {elseif $form.formType == 'real_estate'}不動産所得用
            {else}{$form.formType|escape}{/if}
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-muted">状態</div>
          <div class="fw-semibold">
            {if $form.status == 'draft'}
              <span class="badge text-bg-warning">下書き</span>
            {elseif $form.status == 'finalized'}
              <span class="badge text-bg-success">確定済</span>
            {else}
              {$form.status|escape}
            {/if}
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-muted">確定日時</div>
          <div class="fw-semibold">{if $form.finalizedAt !== ''}{$form.finalizedAt|escape}{else}—{/if}</div>
        </div>
      </div>
    </section>

    <section class="rucaro-card p-3 shadow-sm mb-4">
      <h2 class="h5 mb-3">Page 1: 損益計算書</h2>
      {if isset($form.page1Pl.netIncome)}
        <div class="mb-2">当期純利益（所得金額）: <span class="fw-bold">{$form.page1Pl.netIncome|escape}</span></div>
      {/if}
      <pre class="bg-light p-2 small rounded mb-0" style="max-height:260px;overflow:auto;">{$form.page1Pl|@json_encode:128|escape}</pre>
    </section>

    <section class="rucaro-card p-3 shadow-sm mb-4">
      <h2 class="h5 mb-3">Page 2: 月別売上・仕入・給料賃金</h2>
      <pre class="bg-light p-2 small rounded mb-0" style="max-height:260px;overflow:auto;">{$form.page2Monthly|@json_encode:128|escape}</pre>
    </section>

    <section class="rucaro-card p-3 shadow-sm mb-4">
      <h2 class="h5 mb-3">Page 3: 減価償却費／貸倒引当金／地代家賃／利子割引料／税理士等報酬</h2>
      <pre class="bg-light p-2 small rounded mb-0" style="max-height:260px;overflow:auto;">{$form.page3Breakdown|@json_encode:128|escape}</pre>
    </section>

    <section class="rucaro-card p-3 shadow-sm mb-4">
      <h2 class="h5 mb-3">Page 4: 貸借対照表</h2>
      <pre class="bg-light p-2 small rounded mb-0" style="max-height:260px;overflow:auto;">{$form.page4Bs|@json_encode:128|escape}</pre>
    </section>
  {/if}
{/block}
