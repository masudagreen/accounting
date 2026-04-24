{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">{$asset.assetName|escape} <small class="text-muted">({$asset.assetCode|escape})</small></h1>
      <p class="text-muted mb-0">固定資産の詳細・減価償却スケジュール・編集。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/fixed-assets">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
    </div>
  </header>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <dl class="row g-2 mb-0">
      <dt class="col-sm-2 text-muted">区分</dt>
      <dd class="col-sm-4">{$asset.categoryCode|escape}</dd>
      <dt class="col-sm-2 text-muted">取得日</dt>
      <dd class="col-sm-4"><code>{$asset.acquisitionDate|escape}</code></dd>
      <dt class="col-sm-2 text-muted">事業供用日</dt>
      <dd class="col-sm-4"><code>{$asset.serviceStartDate|escape}</code></dd>
      <dt class="col-sm-2 text-muted">取得原価</dt>
      <dd class="col-sm-4 text-end-md">{$asset.acquisitionCost|escape}</dd>
      <dt class="col-sm-2 text-muted">残存価額</dt>
      <dd class="col-sm-4 text-end-md">{$asset.residualValue|escape}</dd>
      <dt class="col-sm-2 text-muted">耐用年数</dt>
      <dd class="col-sm-4">{$asset.usefulLifeYears}年</dd>
      <dt class="col-sm-2 text-muted">償却方法</dt>
      <dd class="col-sm-4">{$asset.method|escape}</dd>
      <dt class="col-sm-2 text-muted">数量</dt>
      <dd class="col-sm-4">{$asset.quantity}</dd>
      <dt class="col-sm-2 text-muted">状態</dt>
      <dd class="col-sm-10">
        {if $asset.isDisposed}
          <span class="badge text-bg-secondary">除却済 ({$asset.disposalDate|escape})</span>
        {else}
          <span class="badge text-bg-success">稼働中</span>
        {/if}
      </dd>
    </dl>
  </section>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <h2 class="h5 mb-3">減価償却スケジュール</h2>
    {if count($schedules) == 0}
      <p class="text-muted">スケジュールは生成されていません。下記から生成してください。</p>
    {else}
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>#期</th>
              <th>期間</th>
              <th class="text-end">月数</th>
              <th class="text-end">期首簿価</th>
              <th class="text-end">当期償却</th>
              <th class="text-end">累計償却</th>
              <th class="text-end">期末簿価</th>
              <th>計上済</th>
            </tr>
          </thead>
          <tbody>
            {foreach $schedules as $s}
              <tr>
                <td>第 {$s.periodNumber} 期</td>
                <td><code>{$s.periodStartDate|escape}</code> 〜 <code>{$s.periodEndDate|escape}</code></td>
                <td class="text-end">{$s.monthsInService}</td>
                <td class="text-end">{$s.openingBookValue|escape}</td>
                <td class="text-end">{$s.depreciationAmount|escape}</td>
                <td class="text-end">{$s.accumulatedDepreciation|escape}</td>
                <td class="text-end">{$s.closingBookValue|escape}</td>
                <td>{if $s.isPosted}<span class="badge text-bg-success">計上済</span>{else}<span class="badge text-bg-secondary">未計上</span>{/if}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    {/if}
    <form method="post" action="/ui/fixed-assets/{$asset.id|escape}/depreciate" class="d-flex gap-2 justify-content-end pt-3 border-top">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-arrow-clockwise"></i> 現在会計期間でスケジュール生成
      </button>
    </form>
  </section>

  {if !$asset.isDisposed}
    <section class="rucaro-card p-4 shadow-sm mb-4">
      <h2 class="h5 mb-3">編集</h2>
      <form method="post" action="/ui/fixed-assets/{$asset.id|escape}">
        <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">資産名</label>
            <input type="text" name="asset_name" class="form-control" value="{$asset.assetName|escape}">
          </div>
          <div class="col-md-4">
            <label class="form-label">区分コード</label>
            <input type="text" name="category_code" class="form-control" value="{$asset.categoryCode|escape}">
          </div>
          <div class="col-md-4">
            <label class="form-label">残存価額</label>
            <input type="text" inputmode="decimal" name="residual_value" class="form-control text-end" value="{$asset.residualValue|escape}">
          </div>
          <div class="col-md-4">
            <label class="form-label">耐用年数</label>
            <input type="number" min="0" name="useful_life_years" class="form-control text-end" value="{$asset.usefulLifeYears}">
          </div>
          <div class="col-md-4">
            <label class="form-label">償却方法</label>
            <select name="method" class="form-select">
              {foreach $method_options as $opt}
                <option value="{$opt.value|escape}"{if $asset.method == $opt.value} selected{/if}>{$opt.label|escape}</option>
              {/foreach}
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">数量</label>
            <input type="number" min="1" name="quantity" class="form-control text-end" value="{$asset.quantity}">
          </div>
          <div class="col-md-4">
            <label class="form-label">部門コード</label>
            <input type="text" name="department_code" class="form-control" value="{$asset.departmentCode|escape}">
          </div>
          <div class="col-md-12">
            <label class="form-label">備考</label>
            <textarea name="note" class="form-control" rows="2">{$asset.note|escape}</textarea>
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> 更新
          </button>
        </div>
      </form>
    </section>

    <section class="rucaro-card p-4 shadow-sm border-danger">
      <h2 class="h5 mb-2 text-danger">除却</h2>
      <p class="text-muted small">除却日を入力して実行してください。除却後は編集不可になります。</p>
      <form method="post" action="/ui/fixed-assets/{$asset.id|escape}/dispose" class="d-flex gap-2 align-items-end justify-content-end">
        <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
        <div>
          <label class="form-label small">除却日</label>
          <input type="date" name="disposal_date" class="form-control form-control-sm">
        </div>
        <button type="submit" class="btn btn-sm btn-danger">
          <i class="bi bi-box-arrow-down"></i> 除却実行
        </button>
      </form>
    </section>
  {/if}
{/block}
