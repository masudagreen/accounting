{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">複数期比較決算書</h1>
      <p class="text-muted mb-0 small">
        会計期を最大 5 期まで横並びで比較します（右端が最新期）。
      </p>
    </div>
    <div>
      {if $has_multi}
        <a class="btn btn-outline-primary btn-sm"
           href="/ui/fs/multi?format=pdf&termIds={$term_ids_csv|escape}&kind={$kind|escape}">
          <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
        </a>
      {/if}
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/fs/multi" class="row g-2 align-items-end">
      <div class="col-md-6">
        <label for="fs-multi-terms" class="form-label small text-muted mb-1">
          会計期 ID（カンマ区切り、最大 5 件）
        </label>
        <input id="fs-multi-terms" name="termIds" class="form-control form-control-sm"
               value="{$term_ids_csv|escape}" placeholder="01HW...,01HW...">
      </div>
      <div class="col-md-3">
        <label for="fs-multi-kind" class="form-label small text-muted mb-1">種類</label>
        <select id="fs-multi-kind" name="kind" class="form-select form-select-sm">
          <option value="ALL" {if $kind == 'ALL'}selected{/if}>すべて（BS+PL+CS）</option>
          <option value="BS"  {if $kind == 'BS'}selected{/if}>貸借対照表</option>
          <option value="PL"  {if $kind == 'PL'}selected{/if}>損益計算書</option>
          <option value="CS"  {if $kind == 'CS'}selected{/if}>キャッシュフロー計算書</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 比較
        </button>
        <a href="/ui/fs/multi" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if $error_message !== ''}
    <div class="alert alert-warning small mb-4">
      <i class="bi bi-exclamation-triangle"></i> {$error_message|escape}
    </div>
  {/if}

  {if $has_multi}
    {* helper: print one table per kind across all periods *}
    {if $kind_is_bs}
      <section class="rucaro-card p-3 shadow-sm mb-4">
        <h2 class="h5 mb-3">貸借対照表</h2>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr class="table-light">
                <th>項目</th>
                {foreach $periods as $p}
                  <th class="text-end">{$p.label|escape}<br><small class="text-muted">{$p.toDate|escape}</small></th>
                {/foreach}
              </tr>
            </thead>
            <tbody>
              {foreach $periods[0].bs as $code => $sec}
                <tr class="table-secondary fw-semibold">
                  <td>{$sec.label|escape}</td>
                  {foreach $periods as $p}
                    <td class="text-end">{if isset($p.bs[$code])}{$p.bs[$code].subtotal|escape}{else}—{/if}</td>
                  {/foreach}
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </section>
    {/if}

    {if $kind_is_pl}
      <section class="rucaro-card p-3 shadow-sm mb-4">
        <h2 class="h5 mb-3">損益計算書</h2>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr class="table-light">
                <th>項目</th>
                {foreach $periods as $p}
                  <th class="text-end">{$p.label|escape}<br><small class="text-muted">{$p.fromDate|escape} 〜 {$p.toDate|escape}</small></th>
                {/foreach}
              </tr>
            </thead>
            <tbody>
              {foreach $periods[0].pl as $code => $sec}
                <tr class="table-secondary fw-semibold">
                  <td>{$sec.label|escape}</td>
                  {foreach $periods as $p}
                    <td class="text-end">{if isset($p.pl[$code])}{$p.pl[$code].subtotal|escape}{else}—{/if}</td>
                  {/foreach}
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </section>
    {/if}

    {if $kind_is_cs}
      <section class="rucaro-card p-3 shadow-sm mb-4">
        <h2 class="h5 mb-3">キャッシュフロー計算書</h2>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr class="table-light">
                <th>項目</th>
                {foreach $periods as $p}
                  <th class="text-end">{$p.label|escape}<br><small class="text-muted">{$p.toDate|escape}</small></th>
                {/foreach}
              </tr>
            </thead>
            <tbody>
              {foreach $periods[0].cs as $code => $sec}
                <tr class="table-secondary fw-semibold">
                  <td>{$sec.label|escape}</td>
                  {foreach $periods as $p}
                    <td class="text-end">{if isset($p.cs[$code])}{$p.cs[$code].subtotal|escape}{else}—{/if}</td>
                  {/foreach}
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </section>
    {/if}
  {/if}
{/block}
