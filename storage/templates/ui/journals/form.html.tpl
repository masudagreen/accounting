{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}新規仕訳{else}仕訳編集{/if}
      </h1>
      <p class="text-muted mb-0">借方 / 貸方の合計が一致すると保存できます。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/journals">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">
      {foreach $form_errors['_'] as $msg}<div>{$msg|escape}</div>{/foreach}
    </div>
  {/if}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm" id="journal-form">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">

    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <label for="journal_date" class="form-label">発生日</label>
        <input type="date" id="journal_date" name="journal_date"
               value="{$form_journal.journalDate|escape}"
               class="form-control{if isset($form_errors.journal_date)} is-invalid{/if}"
               {if !$can_edit}readonly{/if}>
        {if isset($form_errors.journal_date)}
          <div class="invalid-feedback">{foreach $form_errors.journal_date as $m}{$m|escape}<br>{/foreach}</div>
        {/if}
      </div>
      <div class="col-md-4">
        <label for="fiscal_term_id" class="form-label">会計期間</label>
        <select id="fiscal_term_id" name="fiscal_term_id"
                class="form-select{if isset($form_errors.fiscal_term_id)} is-invalid{/if}"
                {if !$can_edit || $form_mode == 'edit'}disabled{/if}>
          <option value="">（選択してください）</option>
          {foreach $fiscal_terms as $t}
            <option value="{$t.id|escape}"{if $form_journal.fiscalTermId == $t.id} selected{/if}>
              第 {$t.fiscalPeriod} 期 ({$t.startDate|escape} 〜 {$t.endDate|escape})
            </option>
          {/foreach}
        </select>
        {if $form_mode == 'edit'}
          <input type="hidden" name="fiscal_term_id" value="{$form_journal.fiscalTermId|escape}">
        {/if}
        {if isset($form_errors.fiscal_term_id)}
          <div class="invalid-feedback">{foreach $form_errors.fiscal_term_id as $m}{$m|escape}<br>{/foreach}</div>
        {/if}
      </div>
      <div class="col-md-5">
        <label for="summary" class="form-label">摘要</label>
        <input type="text" id="summary" name="summary"
               value="{$form_journal.summary|escape}"
               class="form-control{if isset($form_errors.summary)} is-invalid{/if}"
               maxlength="500"
               {if !$can_edit}readonly{/if}>
        {if isset($form_errors.summary)}
          <div class="invalid-feedback">{foreach $form_errors.summary as $m}{$m|escape}<br>{/foreach}</div>
        {/if}
      </div>
    </div>

    <div class="mb-2 d-flex justify-content-between align-items-center">
      <h2 class="h6 m-0">明細行</h2>
      {if $can_edit}
        <button type="button" class="btn btn-sm btn-outline-secondary" id="add-line-btn">
          <i class="bi bi-plus"></i> 行を追加
        </button>
      {/if}
    </div>
    {if isset($form_errors.lines)}
      <div class="alert alert-danger py-2 small mb-2">
        {foreach $form_errors.lines as $m}{$m|escape}<br>{/foreach}
      </div>
    {/if}

    <div class="table-responsive">
      <table class="table table-sm align-middle" id="lines-table">
        <thead class="table-light">
          <tr>
            <th style="width: 100px;">借/貸</th>
            <th>勘定科目</th>
            <th style="width: 160px;" class="text-end">金額</th>
            <th>メモ</th>
            {if $can_edit}<th style="width: 50px;"></th>{/if}
          </tr>
        </thead>
        <tbody>
          {foreach $form_lines as $idx => $line}
            <tr class="line-row">
              <td>
                <select name="lines[{$idx}][side]" class="form-select form-select-sm"{if !$can_edit} disabled{/if}>
                  <option value="debit"{if $line.side == 'debit'} selected{/if}>借方</option>
                  <option value="credit"{if $line.side == 'credit'} selected{/if}>貸方</option>
                </select>
              </td>
              <td>
                <select name="lines[{$idx}][account_title_id]" class="form-select form-select-sm"{if !$can_edit} disabled{/if}>
                  <option value="">（選択）</option>
                  {foreach $account_titles as $a}
                    <option value="{$a.id|escape}"{if $line.account_title_id == $a.id} selected{/if}>{$a.code|escape} {$a.name|escape}</option>
                  {/foreach}
                </select>
              </td>
              <td>
                <input type="text" inputmode="decimal" name="lines[{$idx}][amount]"
                       value="{$line.amount|escape}"
                       class="form-control form-control-sm text-end line-amount"
                       {if !$can_edit}readonly{/if}>
              </td>
              <td>
                <input type="text" name="lines[{$idx}][memo]" value="{$line.memo|escape}"
                       class="form-control form-control-sm" maxlength="500"
                       {if !$can_edit}readonly{/if}>
              </td>
              {if $can_edit}
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-line-btn" aria-label="行を削除">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              {/if}
            </tr>
          {/foreach}
        </tbody>
        <tfoot>
          <tr class="table-light">
            <th colspan="2" class="text-end">借方合計</th>
            <th class="text-end"><span id="debit-total">0</span></th>
            <th colspan="{if $can_edit}2{else}1{/if}"></th>
          </tr>
          <tr class="table-light">
            <th colspan="2" class="text-end">貸方合計</th>
            <th class="text-end"><span id="credit-total">0</span></th>
            <th colspan="{if $can_edit}2{else}1{/if}"></th>
          </tr>
          <tr>
            <th colspan="2" class="text-end">差額</th>
            <th class="text-end" id="balance-cell"><span id="balance-total">0</span></th>
            <th colspan="{if $can_edit}2{else}1{/if}"></th>
          </tr>
        </tfoot>
      </table>
    </div>

    {if $can_edit}
      <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <a href="/ui/journals" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> 保存（ドラフト）
        </button>
      </div>
    {else}
      <div class="text-muted small pt-3 border-top">この仕訳は読み取り専用です。</div>
    {/if}
  </form>

  <template id="line-row-template">
    <tr class="line-row">
      <td>
        <select name="lines[__IDX__][side]" class="form-select form-select-sm">
          <option value="debit">借方</option>
          <option value="credit">貸方</option>
        </select>
      </td>
      <td>
        <select name="lines[__IDX__][account_title_id]" class="form-select form-select-sm">
          <option value="">（選択）</option>
          {foreach $account_titles as $a}
            <option value="{$a.id|escape}">{$a.code|escape} {$a.name|escape}</option>
          {/foreach}
        </select>
      </td>
      <td>
        <input type="text" inputmode="decimal" name="lines[__IDX__][amount]"
               value="" class="form-control form-control-sm text-end line-amount">
      </td>
      <td>
        <input type="text" name="lines[__IDX__][memo]" value=""
               class="form-control form-control-sm" maxlength="500">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-line-btn" aria-label="行を削除">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    </tr>
  </template>

  <script>
    (function () {
      var table = document.getElementById('lines-table');
      if (!table) return;
      var tbody = table.querySelector('tbody');
      var template = document.getElementById('line-row-template');
      var addBtn = document.getElementById('add-line-btn');
      var debitCell = document.getElementById('debit-total');
      var creditCell = document.getElementById('credit-total');
      var balanceCell = document.getElementById('balance-total');
      var balanceWrapper = document.getElementById('balance-cell');

      function formatYen(n) {
        return n.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
      }

      function recompute() {
        var debit = 0, credit = 0;
        tbody.querySelectorAll('tr.line-row').forEach(function (row) {
          var sideEl = row.querySelector('select[name$="[side]"]');
          var amountEl = row.querySelector('input.line-amount');
          if (!sideEl || !amountEl) return;
          var raw = (amountEl.value || '').replace(/[,\s　]/g, '');
          var n = parseFloat(raw);
          if (isNaN(n) || !isFinite(n)) n = 0;
          if (sideEl.value === 'debit') debit += n;
          else credit += n;
        });
        debitCell.textContent = formatYen(debit);
        creditCell.textContent = formatYen(credit);
        balanceCell.textContent = formatYen(debit - credit);
        if (balanceWrapper) {
          balanceWrapper.classList.toggle('table-success', Math.abs(debit - credit) < 0.0001 && debit > 0);
          balanceWrapper.classList.toggle('table-danger',  Math.abs(debit - credit) >= 0.0001);
        }
      }

      function reindex() {
        tbody.querySelectorAll('tr.line-row').forEach(function (row, idx) {
          row.querySelectorAll('[name]').forEach(function (el) {
            el.name = el.name.replace(/lines\[(?:[^\]]*)\]/, 'lines[' + idx + ']');
          });
        });
      }

      function wireRow(row) {
        row.querySelectorAll('input, select').forEach(function (el) {
          el.addEventListener('input', recompute);
          el.addEventListener('change', recompute);
        });
        var del = row.querySelector('.remove-line-btn');
        if (del) {
          del.addEventListener('click', function () {
            if (tbody.querySelectorAll('tr.line-row').length <= 2) {
              row.querySelectorAll('input').forEach(function (el) { el.value = ''; });
              recompute();
              return;
            }
            row.remove();
            reindex();
            recompute();
          });
        }
      }

      tbody.querySelectorAll('tr.line-row').forEach(wireRow);
      recompute();

      if (addBtn && template && 'content' in template) {
        addBtn.addEventListener('click', function () {
          var idx = tbody.querySelectorAll('tr.line-row').length;
          var clone = template.content.firstElementChild.cloneNode(true);
          clone.outerHTML = clone.outerHTML.replace(/__IDX__/g, String(idx));
          var tmp = document.createElement('tbody');
          tmp.innerHTML = template.innerHTML.replace(/__IDX__/g, String(idx));
          var newRow = tmp.querySelector('tr.line-row');
          tbody.appendChild(newRow);
          wireRow(newRow);
          recompute();
        });
      }
    })();
  </script>
{/block}
