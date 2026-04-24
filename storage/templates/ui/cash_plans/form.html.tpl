{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">新規資金繰り計画</h1>
      <p class="text-muted mb-0">6 区分 × 12 ヶ月の金額を入力します。ラベルを入れた行だけが保存されます。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/cash-plans"><i class="bi bi-arrow-left"></i> 一覧へ戻る</a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">{foreach $form_errors['_'] as $m}<div>{$m|escape}</div>{/foreach}</div>
  {/if}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">計画名</label>
        <input type="text" name="name" value="{$form_name|escape}" class="form-control{if isset($form_errors.name)} is-invalid{/if}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">期首残高</label>
        <input type="text" inputmode="decimal" name="opening_balance" value="{$form_opening_balance|escape}" class="form-control text-end">
      </div>
      <div class="col-md-3">
        <label class="form-label">通貨</label>
        <input type="text" name="currency_code" value="{$form_currency|escape}" class="form-control" maxlength="3" pattern="[A-Z]{literal}{3}{/literal}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">会計期間</label>
        <select name="fiscal_term_id" class="form-select{if isset($form_errors.fiscal_term_id)} is-invalid{/if}" required>
          <option value="">（選択してください）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $form_fiscal_term_id == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-12">
        <label class="form-label">メモ</label>
        <textarea name="notes" class="form-control" rows="2">{$form_notes|escape}</textarea>
      </div>
    </div>

    <h2 class="h6 mb-2">明細 (6 区分 × 12 ヶ月)</h2>
    {if isset($form_errors.entries)}
      <div class="alert alert-danger py-2 small mb-2">{foreach $form_errors.entries as $m}{$m|escape}<br>{/foreach}</div>
    {/if}

    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle" id="cashplan-entries-table">
        <thead class="table-light">
          <tr>
            <th style="min-width: 160px">区分</th>
            <th style="min-width: 180px">ラベル</th>
            {section name=m start=1 loop=13 step=1}<th class="text-end">{$smarty.section.m.index}月</th>{/section}
            <th style="min-width: 160px">メモ</th>
          </tr>
        </thead>
        <tbody>
          {foreach $form_entries as $idx => $e}
            <tr>
              <td>
                <select name="entries[{$idx}][category]" class="form-select form-select-sm">
                  {foreach $category_options as $co}
                    <option value="{$co.value|escape}"{if $e.category == $co.value} selected{/if}>{$co.label|escape}</option>
                  {/foreach}
                </select>
              </td>
              <td><input type="text" name="entries[{$idx}][label]" value="{$e.label|escape}" class="form-control form-control-sm"></td>
              {section name=m start=0 loop=12 step=1}
                <td><input type="text" inputmode="decimal" name="entries[{$idx}][monthly][{$smarty.section.m.index}]"
                           value="{$e.monthly[$smarty.section.m.index]|escape}" class="form-control form-control-sm text-end" size="8"></td>
              {/section}
              <td><input type="text" name="entries[{$idx}][memo]" value="{$e.memo|escape}" class="form-control form-control-sm" maxlength="255"></td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
    <div class="text-end mb-3">
      <button type="button" class="btn btn-sm btn-outline-secondary" id="add-entry-btn"><i class="bi bi-plus"></i> 行を追加</button>
    </div>

    <template id="cashplan-row-template">
      <tr>
        <td>
          <select name="entries[__IDX__][category]" class="form-select form-select-sm">
            {foreach $category_options as $co}
              <option value="{$co.value|escape}">{$co.label|escape}</option>
            {/foreach}
          </select>
        </td>
        <td><input type="text" name="entries[__IDX__][label]" value="" class="form-control form-control-sm"></td>
        {section name=m start=0 loop=12 step=1}
          <td><input type="text" inputmode="decimal" name="entries[__IDX__][monthly][{$smarty.section.m.index}]" value="" class="form-control form-control-sm text-end" size="8"></td>
        {/section}
        <td><input type="text" name="entries[__IDX__][memo]" value="" class="form-control form-control-sm" maxlength="255"></td>
      </tr>
    </template>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
      <a href="/ui/cash-plans" class="btn btn-outline-secondary">キャンセル</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 保存</button>
    </div>
  </form>

  <script>
    (function () {
      var tbody = document.querySelector('#cashplan-entries-table tbody');
      var tpl = document.getElementById('cashplan-row-template');
      var btn = document.getElementById('add-entry-btn');
      if (!tbody || !tpl || !btn) return;
      btn.addEventListener('click', function () {
        var idx = tbody.querySelectorAll('tr').length;
        var tmp = document.createElement('tbody');
        tmp.innerHTML = tpl.innerHTML.replace(/__IDX__/g, String(idx));
        var row = tmp.querySelector('tr');
        if (row) tbody.appendChild(row);
      });
    })();
  </script>
{/block}
