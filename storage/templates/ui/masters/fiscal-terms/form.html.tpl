{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}会計期の新規追加{else}会計期の編集{/if}
      </h1>
      <p class="text-muted mb-0">期番号と期間を指定して会計期を登録します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/masters/fiscal-terms">
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
        <label for="fiscal_period" class="form-label">期番号 <span class="text-danger">*</span></label>
        <input type="number" id="fiscal_period" name="fiscal_period" value="{$form_values.fiscal_period|escape}"
               min="1" max="9999"
               class="form-control{if isset($form_errors.fiscal_period)} is-invalid{/if}">
        {if isset($form_errors.fiscal_period)}<div class="invalid-feedback">{foreach $form_errors.fiscal_period as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="start_date" class="form-label">開始日 <span class="text-danger">*</span></label>
        <input type="date" id="start_date" name="start_date" value="{$form_values.start_date|escape}"
               class="form-control{if isset($form_errors.start_date)} is-invalid{/if}">
        {if isset($form_errors.start_date)}<div class="invalid-feedback">{foreach $form_errors.start_date as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="end_date" class="form-label">終了日 <span class="text-danger">*</span></label>
        <input type="date" id="end_date" name="end_date" value="{$form_values.end_date|escape}"
               class="form-control{if isset($form_errors.end_date)} is-invalid{/if}">
        {if isset($form_errors.end_date)}<div class="invalid-feedback">{foreach $form_errors.end_date as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>

      <div class="col-md-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_closed" name="is_closed" value="1"{if $form_values.is_closed == '1'} checked{/if}>
          <label class="form-check-label" for="is_closed">決算締切済としてマークする</label>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="/ui/masters/fiscal-terms" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> {if $form_mode == 'new'}登録する{else}保存する{/if}
      </button>
    </div>
  </form>
{/block}
