{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}補助科目の新規追加{else}補助科目の編集{/if}
      </h1>
      <p class="text-muted mb-0">親の勘定科目とコードを指定して補助科目を登録します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/masters/sub-account-titles">
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
        <label for="account_title_id" class="form-label">親勘定科目 <span class="text-danger">*</span></label>
        <select id="account_title_id" name="account_title_id"
                class="form-select{if isset($form_errors.account_title_id)} is-invalid{/if}"
                {if $form_mode == 'edit'}disabled{/if}>
          <option value="">（選択してください）</option>
          {foreach $title_options as $t}
            <option value="{$t.id|escape}"{if $form_values.account_title_id == $t.id} selected{/if}>{$t.code|escape} — {$t.name|escape}</option>
          {/foreach}
        </select>
        {if $form_mode == 'edit'}
          <input type="hidden" name="account_title_id" value="{$form_values.account_title_id|escape}">
        {/if}
        {if isset($form_errors.account_title_id)}<div class="invalid-feedback">{foreach $form_errors.account_title_id as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="code" class="form-label">コード <span class="text-danger">*</span></label>
        <input type="text" id="code" name="code" value="{$form_values.code|escape}"
               maxlength="16"
               class="form-control{if isset($form_errors.code)} is-invalid{/if}">
        {if isset($form_errors.code)}<div class="invalid-feedback">{foreach $form_errors.code as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>

      <div class="col-md-8">
        <label for="name" class="form-label">名称 <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{$form_values.name|escape}"
               maxlength="128"
               class="form-control{if isset($form_errors.name)} is-invalid{/if}">
        {if isset($form_errors.name)}<div class="invalid-feedback">{foreach $form_errors.name as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="sort_order" class="form-label">並び順</label>
        <input type="number" id="sort_order" name="sort_order" value="{$form_values.sort_order|escape}"
               min="0" max="99999"
               class="form-control">
      </div>
      <div class="col-md-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"{if $form_values.is_active == '1'} checked{/if}>
          <label class="form-check-label" for="is_active">有効にする</label>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="/ui/masters/sub-account-titles" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> {if $form_mode == 'new'}登録する{else}保存する{/if}
      </button>
    </div>
  </form>
{/block}
