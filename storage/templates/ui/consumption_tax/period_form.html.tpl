{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">新規消費税申告期間</h1>
      <p class="text-muted mb-0">課税期間と課税方式 (原則・簡易・2 割特例) を登録します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/consumption-tax/periods"><i class="bi bi-arrow-left"></i> 一覧へ戻る</a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">{foreach $form_errors['_'] as $m}<div>{$m|escape}</div>{/foreach}</div>
  {/if}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
    <div class="row g-3">
      <div class="col-md-12">
        <label class="form-label">会計期間</label>
        <select name="fiscal_term_id" class="form-select{if isset($form_errors.fiscal_term_id)} is-invalid{/if}" required>
          <option value="">（選択してください）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $form.fiscalTermId == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">期間開始</label>
        <input type="date" name="period_from" value="{$form.periodFrom|escape}" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">期間終了</label>
        <input type="date" name="period_to" value="{$form.periodTo|escape}" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">課税方式</label>
        <select name="method" class="form-select">
          {foreach $method_options as $opt}
            <option value="{$opt.value|escape}"{if $form.method == $opt.value} selected{/if}>{$opt.label|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">事業区分 (簡易課税時のみ)</label>
        <select name="simplified_business_category" class="form-select">
          <option value="">（なし）</option>
          {foreach $category_options as $opt}
            <option value="{$opt.value|escape}"{if $form.simplifiedBusinessCategory == $opt.value} selected{/if}>{$opt.label|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="is_interim" name="is_interim"
                 {if $form.isInterim == '1'}checked{/if}>
          <label class="form-check-label" for="is_interim">中間申告</label>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
      <a href="/ui/consumption-tax/periods" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 登録</button>
    </div>
  </form>
{/block}
