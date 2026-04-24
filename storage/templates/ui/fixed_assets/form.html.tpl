{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">{if $form_mode == 'new'}新規固定資産{else}固定資産編集{/if}</h1>
      <p class="text-muted mb-0">資産コード・取得日・償却方法・耐用年数を入力してください。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/fixed-assets">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">
      {foreach $form_errors['_'] as $m}<div>{$m|escape}</div>{/foreach}
    </div>
  {/if}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">資産コード</label>
        <input type="text" name="asset_code" class="form-control{if isset($form_errors.asset_code)} is-invalid{/if}"
               value="{$form.assetCode|escape}" required>
        {if isset($form_errors.asset_code)}<div class="invalid-feedback">{foreach $form_errors.asset_code as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-8">
        <label class="form-label">資産名</label>
        <input type="text" name="asset_name" class="form-control{if isset($form_errors.asset_name)} is-invalid{/if}"
               value="{$form.assetName|escape}" required>
        {if isset($form_errors.asset_name)}<div class="invalid-feedback">{foreach $form_errors.asset_name as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label class="form-label">区分コード</label>
        <input type="text" name="category_code" class="form-control{if isset($form_errors.category_code)} is-invalid{/if}"
               value="{$form.categoryCode|escape}" placeholder="例: machinery">
      </div>
      <div class="col-md-4">
        <label class="form-label">取得日</label>
        <input type="date" name="acquisition_date" class="form-control{if isset($form_errors.acquisition_date)} is-invalid{/if}"
               value="{$form.acquisitionDate|escape}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">事業供用日</label>
        <input type="date" name="service_start_date" class="form-control{if isset($form_errors.service_start_date)} is-invalid{/if}"
               value="{$form.serviceStartDate|escape}">
      </div>
      <div class="col-md-4">
        <label class="form-label">取得原価</label>
        <input type="text" inputmode="decimal" name="acquisition_cost" class="form-control text-end"
               value="{$form.acquisitionCost|escape}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">残存価額</label>
        <input type="text" inputmode="decimal" name="residual_value" class="form-control text-end"
               value="{$form.residualValue|escape}">
      </div>
      <div class="col-md-4">
        <label class="form-label">耐用年数</label>
        <input type="number" min="0" name="useful_life_years" class="form-control text-end"
               value="{$form.usefulLifeYears|escape}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">償却方法</label>
        <select name="method" class="form-select">
          {foreach $method_options as $opt}
            <option value="{$opt.value|escape}"{if $form.method == $opt.value} selected{/if}>{$opt.label|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">数量</label>
        <input type="number" min="1" name="quantity" class="form-control text-end" value="{$form.quantity|escape}">
      </div>
      <div class="col-md-4">
        <label class="form-label">部門コード</label>
        <input type="text" name="department_code" class="form-control" value="{$form.departmentCode|escape}">
      </div>
      <div class="col-md-12">
        <label class="form-label">備考</label>
        <textarea name="note" class="form-control" rows="2">{$form.note|escape}</textarea>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
      <a href="/ui/fixed-assets" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> 登録
      </button>
    </div>
  </form>
{/block}
