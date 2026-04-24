{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}事業主の新規追加{else}事業主の編集{/if}
      </h1>
      <p class="text-muted mb-0">会計主体の基本情報を登録します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/masters/entities">
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
      <div class="col-md-8">
        <label for="name" class="form-label">屋号 / 会社名 <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{$form_values.name|escape}" maxlength="128"
               class="form-control{if isset($form_errors.name)} is-invalid{/if}">
        {if isset($form_errors.name)}<div class="invalid-feedback">{foreach $form_errors.name as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="is_corporate" class="form-label">区分</label>
        <select id="is_corporate" name="is_corporate" class="form-select">
          <option value="1"{if $form_values.is_corporate == '1'} selected{/if}>法人</option>
          <option value="0"{if $form_values.is_corporate == '0'} selected{/if}>個人事業主</option>
        </select>
      </div>

      <div class="col-md-4">
        <label for="nation_code" class="form-label">国コード (ISO 3166-1 alpha-3)</label>
        <input type="text" id="nation_code" name="nation_code" value="{$form_values.nation_code|escape}"
               maxlength="3" pattern="[A-Za-z]{literal}{3}{/literal}"
               class="form-control text-uppercase{if isset($form_errors.nation_code)} is-invalid{/if}">
        {if isset($form_errors.nation_code)}<div class="invalid-feedback">{foreach $form_errors.nation_code as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="currency_code" class="form-label">通貨コード (ISO 4217)</label>
        <input type="text" id="currency_code" name="currency_code" value="{$form_values.currency_code|escape}"
               maxlength="3" pattern="[A-Za-z]{literal}{3}{/literal}"
               class="form-control text-uppercase{if isset($form_errors.currency_code)} is-invalid{/if}">
        {if isset($form_errors.currency_code)}<div class="invalid-feedback">{foreach $form_errors.currency_code as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="fiscal_start_mmdd" class="form-label">会計年度開始 MMDD</label>
        <input type="text" id="fiscal_start_mmdd" name="fiscal_start_mmdd" value="{$form_values.fiscal_start_mmdd|escape}"
               maxlength="4" pattern="\d{literal}{4}{/literal}"
               class="form-control{if isset($form_errors.fiscal_start_mmdd)} is-invalid{/if}">
        {if isset($form_errors.fiscal_start_mmdd)}<div class="invalid-feedback">{foreach $form_errors.fiscal_start_mmdd as $m}{$m|escape}<br>{/foreach}</div>{/if}
        <div class="form-text">例: 0101 (1月1日) / 0401 (4月1日)</div>
      </div>

      <div class="col-md-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"{if $form_values.is_active == '1'} checked{/if}>
          <label class="form-check-label" for="is_active">有効にする</label>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="/ui/masters/entities" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> {if $form_mode == 'new'}登録する{else}保存する{/if}
      </button>
    </div>
  </form>
{/block}
