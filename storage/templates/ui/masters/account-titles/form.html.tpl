{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}勘定科目の新規追加{else}勘定科目の編集{/if}
      </h1>
      <p class="text-muted mb-0">コードは事業者内で一意です。分類と貸借は登録後も変更できます。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/masters/account-titles">
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
      <div class="col-md-3">
        <label for="code" class="form-label">コード <span class="text-danger">*</span></label>
        <input type="text" id="code" name="code" value="{$form_values.code|escape}"
               maxlength="16"
               class="form-control{if isset($form_errors.code)} is-invalid{/if}">
        {if isset($form_errors.code)}<div class="invalid-feedback">{foreach $form_errors.code as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-9">
        <label for="name" class="form-label">名称 <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{$form_values.name|escape}"
               maxlength="128"
               class="form-control{if isset($form_errors.name)} is-invalid{/if}">
        {if isset($form_errors.name)}<div class="invalid-feedback">{foreach $form_errors.name as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>

      <div class="col-md-4">
        <label for="category" class="form-label">分類 <span class="text-danger">*</span></label>
        <select id="category" name="category" class="form-select{if isset($form_errors.category)} is-invalid{/if}">
          {foreach $categories as $c}
            <option value="{$c|escape}"{if $form_values.category == $c} selected{/if}>{$category_labels[$c]|default:$c|escape}</option>
          {/foreach}
        </select>
        {if isset($form_errors.category)}<div class="invalid-feedback">{foreach $form_errors.category as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="normal_side" class="form-label">貸借区分 <span class="text-danger">*</span></label>
        <select id="normal_side" name="normal_side" class="form-select{if isset($form_errors.normal_side)} is-invalid{/if}">
          {foreach $normal_sides as $s}
            <option value="{$s|escape}"{if $form_values.normal_side == $s} selected{/if}>{$normal_side_labels[$s]|default:$s|escape}</option>
          {/foreach}
        </select>
        {if isset($form_errors.normal_side)}<div class="invalid-feedback">{foreach $form_errors.normal_side as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4">
        <label for="sort_order" class="form-label">並び順</label>
        <input type="number" id="sort_order" name="sort_order" value="{$form_values.sort_order|escape}"
               min="0" max="99999"
               class="form-control{if isset($form_errors.sort_order)} is-invalid{/if}">
        {if isset($form_errors.sort_order)}<div class="invalid-feedback">{foreach $form_errors.sort_order as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>

      <div class="col-md-8">
        <label for="parent_id" class="form-label">親勘定科目</label>
        <select id="parent_id" name="parent_id" class="form-select{if isset($form_errors.parent_id)} is-invalid{/if}">
          <option value="">（なし・最上位）</option>
          {foreach $parent_options as $p}
            <option value="{$p.id|escape}"{if $form_values.parent_id == $p.id} selected{/if}>{$p.code|escape} — {$p.name|escape}</option>
          {/foreach}
        </select>
        {if isset($form_errors.parent_id)}<div class="invalid-feedback">{foreach $form_errors.parent_id as $m}{$m|escape}<br>{/foreach}</div>{/if}
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"{if $form_values.is_active == '1'} checked{/if}>
          <label class="form-check-label" for="is_active">有効にする</label>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="/ui/masters/account-titles" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> {if $form_mode == 'new'}登録する{else}保存する{/if}
      </button>
    </div>
  </form>
{/block}
