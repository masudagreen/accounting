{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">{if $form_mode == 'new'}新規純資産変動調整{else}純資産変動調整編集{/if}</h1>
      <p class="text-muted mb-0">株主資本等変動計算書の列 (section) と変動事由 (change type) を指定してください。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/ss-adjustments"><i class="bi bi-arrow-left"></i> 一覧へ戻る</a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">{foreach $form_errors['_'] as $m}<div>{$m|escape}</div>{/foreach}</div>
  {/if}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
    <div class="row g-3">
      {if $form_mode == 'new'}
        <div class="col-md-12">
          <label class="form-label">会計期間</label>
          <select name="fiscal_term_id" class="form-select{if isset($form_errors.fiscal_term_id)} is-invalid{/if}" required>
            <option value="">（選択してください）</option>
            {foreach $fiscal_terms as $t}
              <option value="{$t.id|escape}"{if $form.fiscalTermId == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
            {/foreach}
          </select>
        </div>
      {/if}
      <div class="col-md-6">
        <label class="form-label">列 (section)</label>
        <select name="section_code" class="form-select{if isset($form_errors.section_code)} is-invalid{/if}">
          {foreach $section_options as $opt}
            <option value="{$opt.value|escape}"{if $form.sectionCode == $opt.value} selected{/if}>{$opt.label|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">変動事由</label>
        <select name="change_type" class="form-select{if isset($form_errors.change_type)} is-invalid{/if}">
          {foreach $change_options as $opt}
            <option value="{$opt.value|escape}"{if $form.changeType == $opt.value} selected{/if}>{$opt.label|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label">項目名 (label)</label>
        <input type="text" name="label" value="{$form.label|escape}" class="form-control{if isset($form_errors.label)} is-invalid{/if}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">金額</label>
        <input type="text" inputmode="decimal" name="amount" value="{$form.amount|escape}" class="form-control text-end">
      </div>
      <div class="col-md-2">
        <label class="form-label">並び順</label>
        <input type="number" min="0" name="sort_order" value="{$form.sortOrder|escape}" class="form-control text-end">
      </div>
      <div class="col-md-10">
        <label class="form-label">メモ</label>
        <input type="text" name="notes" value="{$form.notes|escape}" class="form-control" maxlength="255">
      </div>
    </div>

    <div class="d-flex justify-content-between pt-3 mt-3 border-top">
      {if $form_mode == 'edit'}
        <form method="post" action="/ui/ss-adjustments/{$form.id}/delete"
              onsubmit="return confirm('この調整行を削除しますか？');">
          <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
          <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> 削除</button>
        </form>
      {else}
        <span></span>
      {/if}
      <div class="d-flex gap-2">
        <a href="/ui/ss-adjustments" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> {if $form_mode == 'edit'}更新{else}登録{/if}</button>
      </div>
    </div>
  </form>
{/block}
