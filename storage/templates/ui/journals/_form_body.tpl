
  {if isset($form_errors['_'])}
    <div class="alert alert-danger">
      {foreach $form_errors['_'] as $msg}<div>{$msg|escape}</div>{/foreach}
    </div>
  {/if}

  {if !isset($fiscal_terms) || count($fiscal_terms) == 0}
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle"></i>
      この事業者には会計期間が登録されていません。先に会計期間を作成してください。
    </div>
  {/if}

  {* The in-form duplicate selector was moved to the journal list as a
     per-row 「複製」 button. Operators land on this page via
     /ui/journals/new?duplicate_from=ID with the source's lines already
     populated, date reset to today. *}

  <form method="post" action="{$form_action|escape}" class="rucaro-card p-4 shadow-sm" id="journal-form">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">

    {* The fiscal_term is fixed to whatever the navbar selector shows; the
       form just round-trips the id through a hidden field so submit hits
       the same period without forcing the operator to pick it twice. *}
    <input type="hidden" name="fiscal_term_id" value="{$form_journal.fiscalTermId|escape}">

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
      <div class="col-md-9">
        <label for="summary" class="form-label">摘要 <span class="text-muted small">（任意）</span></label>
        <input type="text" id="summary" name="summary"
               value="{$form_journal.summary|escape}"
               class="form-control{if isset($form_errors.summary)} is-invalid{/if}"
               maxlength="500"
               {if !$can_edit}readonly{/if}>
        {if isset($form_errors.summary)}
          <div class="invalid-feedback">{foreach $form_errors.summary as $m}{$m|escape}<br>{/foreach}</div>
        {/if}
        {if isset($form_errors.fiscal_term_id)}
          {* fiscal_term_id is set automatically; this branch only fires if
             the entity has no terms at all (the alert above already warns). *}
          <div class="text-danger small mt-1">{foreach $form_errors.fiscal_term_id as $m}{$m|escape}<br>{/foreach}</div>
        {/if}
      </div>
    </div>

    {* Admin fast-path: skip draft → approved → posted on submit and
       persist as posted directly. Non-admin sees the same checkbox but
       disabled + unchecked so the workflow is documented in the UI even
       when it's not available to them. The backend re-checks the role
       on submit, so a forged `skip_approval=1` from a non-admin POST is
       silently ignored. Only meaningful in 新規 mode (form_mode='new'). *}
    {if isset($form_mode) && $form_mode == 'new'}
      <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="skip_approval" id="skip_approval" value="1"
                 {if isset($is_admin) && $is_admin}checked{else}disabled{/if}>
          <label class="form-check-label" for="skip_approval">
            <i class="bi bi-lightning-charge"></i> 承認スキップ（draft → posted を一括）
            {if !isset($is_admin) || !$is_admin}
              <span class="text-muted small">— 管理者のみ操作可能</span>
            {/if}
          </label>
        </div>
      </div>
    {/if}

    <div class="mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h2 class="h6 m-0">明細</h2>
      <div class="d-flex gap-2 align-items-center">
        {if $can_edit}
          <div class="btn-group btn-group-sm" role="group" aria-label="税計算方式">
            <input type="radio" class="btn-check" name="tax-mode" id="tax-mode-inclusive" value="inclusive" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="tax-mode-inclusive" title="入力金額を税込として税額を逆算します">税込入力</label>
            <input type="radio" class="btn-check" name="tax-mode" id="tax-mode-exclusive" value="exclusive" autocomplete="off">
            <label class="btn btn-outline-secondary" for="tax-mode-exclusive" title="入力金額を税抜として税額を加算します">税抜入力</label>
          </div>
        {/if}
      </div>
    </div>
    {if isset($form_errors.lines)}
      <div class="alert alert-danger py-2 small mb-2">
        {foreach $form_errors.lines as $m}{$m|escape}<br>{/foreach}
      </div>
    {/if}

    <div class="row g-3">
      {* ============ 借方 (Debit) — 左（簿記の標準位置） ============ *}
      <div class="col-lg-6">
        <div class="journal-side-card debit">
          <div class="journal-side-header d-flex align-items-center justify-content-between">
            <span class="badge text-bg-primary side-badge">借方 (Debit)</span>
            {if $can_edit}
              <button type="button" class="btn btn-sm btn-outline-secondary side-add-btn" data-side="debit">
                <i class="bi bi-plus"></i> 行を追加
              </button>
            {/if}
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle journal-side-table" data-side="debit">
              <thead class="table-light">
                <tr>
                  <th class="th-num">#</th>
                  <th class="th-account">勘定科目</th>
                  <th class="th-sub">補助</th>
                  <th class="th-amount text-end">金額</th>
                  <th class="th-tax">税区分</th>
                  <th class="th-memo">メモ</th>
                  {if $can_edit}<th class="th-ops"></th>{/if}
                </tr>
              </thead>
              <tbody>
                {foreach $form_debit_lines as $idx => $line}
                  {include file="journals/_form_line_row.tpl"
                           side="debit"
                           idx=$idx
                           line=$line
                           can_edit=$can_edit
                           account_titles=$account_titles}
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {* ============ 貸方 (Credit) — 右（簿記の標準位置） ============ *}
      <div class="col-lg-6">
        <div class="journal-side-card credit">
          <div class="journal-side-header d-flex align-items-center justify-content-between">
            <span class="badge text-bg-warning side-badge">貸方 (Credit)</span>
            {if $can_edit}
              <button type="button" class="btn btn-sm btn-outline-secondary side-add-btn" data-side="credit">
                <i class="bi bi-plus"></i> 行を追加
              </button>
            {/if}
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle journal-side-table" data-side="credit">
              <thead class="table-light">
                <tr>
                  <th class="th-num">#</th>
                  <th class="th-account">勘定科目</th>
                  <th class="th-sub">補助</th>
                  <th class="th-amount text-end">金額</th>
                  <th class="th-tax">税区分</th>
                  <th class="th-memo">メモ</th>
                  {if $can_edit}<th class="th-ops"></th>{/if}
                </tr>
              </thead>
              <tbody>
                {foreach $form_credit_lines as $idx => $line}
                  {include file="journals/_form_line_row.tpl"
                           side="credit"
                           idx=$idx
                           line=$line
                           can_edit=$can_edit
                           account_titles=$account_titles}
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {if $can_edit}
      <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
        <a href="/ui/journals" class="btn btn-outline-secondary">キャンセル</a>
        <button type="submit" class="btn btn-primary" id="submit-btn">
          <i class="bi bi-save"></i> 保存（ドラフト）
        </button>
      </div>
    {else}
      <div class="text-muted small pt-3 border-top mt-3">この仕訳は読み取り専用です。</div>
    {/if}
  </form>

  {* sticky balance bar — pinned to viewport bottom inside the page content *}
  <div class="balance-sticky-bar mt-3" id="balance-bar" role="status" aria-live="polite">
    <div class="d-flex flex-wrap justify-content-around align-items-center gap-3 p-2">
      <div class="text-center">
        <div class="small text-muted">借方合計</div>
        <div class="h5 mb-0" id="debit-total">0</div>
      </div>
      <div class="text-center">
        <div class="small text-muted">貸方合計</div>
        <div class="h5 mb-0" id="credit-total">0</div>
      </div>
      <div class="text-center" id="balance-cell">
        <div class="small text-muted">差額</div>
        <div class="h5 mb-0">
          <span id="balance-icon"></span>
          <span id="balance-total">0</span>
        </div>
      </div>
    </div>
  </div>

  {* ---- row template ---- *}
  <template id="line-row-template-credit">
    <tr class="line-row" data-side="credit">
      <td class="text-center text-muted small line-num">__NUM__</td>
      <td class="cell-account">
        <input type="hidden" name="lines[__GIDX__][side]" value="credit">
        <input type="hidden" name="lines[__GIDX__][account_title_id]" value="" class="line-account-id">
        <div class="combobox" role="combobox">
          <input type="text" class="form-control form-control-sm line-account-search combobox-input"
                 placeholder="科目を検索 (名前/ローマ字)" autocomplete="off">
          <ul class="combobox-list" hidden></ul>
        </div>
      </td>
      <td class="cell-sub">
        <select name="lines[__GIDX__][sub_account_title_id]" class="form-select form-select-sm line-sub" disabled>
          <option value="">—</option>
        </select>
        <input type="hidden" class="line-sub-current" value="">
      </td>
      <td class="cell-amount">
        <input type="text" inputmode="decimal" name="lines[__GIDX__][amount]"
               value="" class="form-control form-control-sm text-end line-amount">
      </td>
      <td class="cell-tax">
        <select class="form-select form-select-sm line-tax-rate">
          <option value="0|exempt" selected>対象外</option>
          <option value="0|nontax">非課税</option>
          <option value="8|reduced">軽減 8%</option>
          <option value="10|standard">標準 10%</option>
        </select>
        <input type="hidden" name="lines[__GIDX__][tax_rate_percent]" value="0.00" class="line-tax-rate-input">
        <input type="hidden" name="lines[__GIDX__][tax_amount]" value="0.0000" class="line-tax-amount-input">
        <input type="hidden" name="lines[__GIDX__][is_tax_reduced]" value="0" class="line-tax-reduced-input">
      </td>
      <td class="cell-memo">
        <input type="text" name="lines[__GIDX__][memo]" value=""
               class="form-control form-control-sm line-memo" maxlength="500">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-line-btn" aria-label="行を削除">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    </tr>
  </template>
  <template id="line-row-template-debit">
    <tr class="line-row" data-side="debit">
      <td class="text-center text-muted small line-num">__NUM__</td>
      <td class="cell-account">
        <input type="hidden" name="lines[__GIDX__][side]" value="debit">
        <input type="hidden" name="lines[__GIDX__][account_title_id]" value="" class="line-account-id">
        <div class="combobox" role="combobox">
          <input type="text" class="form-control form-control-sm line-account-search combobox-input"
                 placeholder="科目を検索 (名前/ローマ字)" autocomplete="off">
          <ul class="combobox-list" hidden></ul>
        </div>
      </td>
      <td class="cell-sub">
        <select name="lines[__GIDX__][sub_account_title_id]" class="form-select form-select-sm line-sub" disabled>
          <option value="">—</option>
        </select>
        <input type="hidden" class="line-sub-current" value="">
      </td>
      <td class="cell-amount">
        <input type="text" inputmode="decimal" name="lines[__GIDX__][amount]"
               value="" class="form-control form-control-sm text-end line-amount">
      </td>
      <td class="cell-tax">
        <select class="form-select form-select-sm line-tax-rate">
          <option value="0|exempt" selected>対象外</option>
          <option value="0|nontax">非課税</option>
          <option value="8|reduced">軽減 8%</option>
          <option value="10|standard">標準 10%</option>
        </select>
        <input type="hidden" name="lines[__GIDX__][tax_rate_percent]" value="0.00" class="line-tax-rate-input">
        <input type="hidden" name="lines[__GIDX__][tax_amount]" value="0.0000" class="line-tax-amount-input">
        <input type="hidden" name="lines[__GIDX__][is_tax_reduced]" value="0" class="line-tax-reduced-input">
      </td>
      <td class="cell-memo">
        <input type="text" name="lines[__GIDX__][memo]" value=""
               class="form-control form-control-sm line-memo" maxlength="500">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-line-btn" aria-label="行を削除">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    </tr>
  </template>

  <div class="modal fade" id="shortcut-help-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">キーボードショートカット</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table table-sm">
            <tbody>
              <tr><th><kbd>Enter</kbd></th><td>金額欄から次行へ移動。最終行なら新しい行を追加</td></tr>
              <tr><th><kbd>Tab</kbd></th><td>次のフィールドへ移動 (通常通り)</td></tr>
              <tr><th><kbd>Ctrl</kbd>/<kbd>⌘</kbd> + <kbd>S</kbd></th><td>仕訳を保存</td></tr>
              <tr><th><kbd>Esc</kbd></th><td>最終行をクリア / 候補ドロップダウンを閉じる</td></tr>
              <tr><th><kbd>↑</kbd>/<kbd>↓</kbd></th><td>科目候補ドロップダウンの選択を移動</td></tr>
            </tbody>
          </table>
          <p class="small text-muted mb-0">
            勘定科目は名前・コード・ローマ字（例: <code>shoumou</code> → 消耗品費）で検索できます。
          </p>
        </div>
      </div>
    </div>
  </div>

  <script type="application/json" id="account-titles-data">{$account_titles_json nofilter}</script>
  <script type="application/json" id="sub-accounts-data">{$sub_accounts_by_account_json nofilter}</script>
  <script type="application/json" id="tax-defaults-data">{$tax_defaults_json nofilter}</script>

  <style>
    .balance-sticky-bar {
      position: sticky;
      bottom: 0;
      z-index: 1020;
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      box-shadow: 0 -.25rem .75rem rgba(0,0,0,.06);
    }
    .balance-sticky-bar.is-balanced { border-color: #198754; background: #d1e7dd; }
    .balance-sticky-bar.is-unbalanced { border-color: #dc3545; background: #f8d7da; }

    .journal-side-card {
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      padding: .5rem;
      background: #fff;
    }
    .journal-side-card.credit { border-left: 4px solid #ffc107; background: rgba(255, 193, 7, 0.03); }
    .journal-side-card.debit  { border-left: 4px solid #0d6efd; background: rgba(13, 110, 253, 0.03); }
    .journal-side-header { padding: .25rem .5rem .5rem .5rem; }
    .journal-side-header .side-badge { font-size: .9rem; }

    .journal-side-table th.th-num    { width: 28px; }
    .journal-side-table th.th-sub    { width: 70px; }
    .journal-side-table th.th-amount { width: 110px; }
    .journal-side-table th.th-tax    { width: 100px; }
    .journal-side-table th.th-memo   { width: auto; }
    .journal-side-table th.th-ops    { width: 32px; }
    .journal-side-table .line-amount { font-variant-numeric: tabular-nums; }
    .journal-side-table td { vertical-align: middle; }
    .journal-side-table .cell-sub .line-sub { padding-left: .25rem; padding-right: .25rem; }

    .combobox { position: relative; }
    .combobox-list {
      position: fixed;
      z-index: 1500;
      max-height: 280px;
      overflow-y: auto;
      margin: 0;
      padding: 0;
      list-style: none;
      background: #fff;
      border: 1px solid #ced4da;
      border-radius: .25rem;
      box-shadow: 0 .25rem .75rem rgba(0,0,0,.08);
      min-width: 280px;
    }
    .combobox-list li {
      padding: .25rem .5rem;
      cursor: pointer;
      font-size: .875rem;
      white-space: nowrap;
    }
    .combobox-list li.is-active,
    .combobox-list li:hover { background: #cfe2ff; }
    .combobox-list .ct-code   { color: #6c757d; font-family: var(--bs-font-monospace, monospace); font-size: .8rem; margin-right: .5rem; }
    .combobox-list .ct-name   { font-weight: 500; }
    .combobox-list .ct-romaji { color: #adb5bd; font-size: .75rem; margin-left: .5rem; }
  </style>

  <script>
    (function () {
      'use strict';

      var form = document.getElementById('journal-form');
      if (!form) return;
      var submitBtn = document.getElementById('submit-btn');
      var debitCell = document.getElementById('debit-total');
      var creditCell = document.getElementById('credit-total');
      var balanceCell = document.getElementById('balance-total');
      var balanceIcon = document.getElementById('balance-icon');
      var balanceBar = document.getElementById('balance-bar');

      var creditTbody = document.querySelector('table.journal-side-table[data-side="credit"] tbody');
      var debitTbody  = document.querySelector('table.journal-side-table[data-side="debit"]  tbody');
      var creditTpl   = document.getElementById('line-row-template-credit');
      var debitTpl    = document.getElementById('line-row-template-debit');

      var accountTitles = parseJsonScript('account-titles-data', []);
      var subByAccount  = parseJsonScript('sub-accounts-data', {});
      var taxDefaults   = parseJsonScript('tax-defaults-data', {});

      var accountById   = {};
      accountTitles.forEach(function (a) { accountById[a.id] = a; });

      function parseJsonScript(id, fallback) {
        var el = document.getElementById(id);
        if (!el) return fallback;
        try { return JSON.parse(el.textContent || el.innerText || '') ?? fallback; }
        catch (e) { return fallback; }
      }
      function formatYen(n) {
        return n.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
      }
      function parseAmount(raw) {
        var n = parseFloat((raw || '').replace(/[,\s　]/g, ''));
        return (isNaN(n) || !isFinite(n)) ? 0 : n;
      }

      function allRows() {
        var rows = [];
        creditTbody.querySelectorAll('tr.line-row').forEach(function (r) { rows.push(r); });
        debitTbody.querySelectorAll('tr.line-row').forEach(function (r) { rows.push(r); });
        return rows;
      }

      // Re-number line rows globally so `lines[N][...]` indices stay
      // contiguous regardless of which side they came from. PHP's
      // `parse_str` doesn't care about gaps (it'll happily index 0,3,7),
      // but contiguous indices are simpler to debug.
      function reindex() {
        var gidx = 0;
        ['credit', 'debit'].forEach(function (side) {
          var tbody = (side === 'credit') ? creditTbody : debitTbody;
          var n = 0;
          tbody.querySelectorAll('tr.line-row').forEach(function (row) {
            n++;
            var num = row.querySelector('.line-num');
            if (num) num.textContent = String(n);
            row.querySelectorAll('[name]').forEach(function (el) {
              el.name = el.name.replace(/lines\[(?:[^\]]*)\]/, 'lines[' + gidx + ']');
            });
            row.dataset.gidx = String(gidx);
            gidx++;
          });
        });
      }

      function recompute() {
        var debit = 0, credit = 0;
        creditTbody.querySelectorAll('tr.line-row .line-amount').forEach(function (el) { credit += parseAmount(el.value); });
        debitTbody.querySelectorAll('tr.line-row .line-amount').forEach(function (el)  { debit  += parseAmount(el.value); });
        creditCell.textContent = formatYen(credit);
        debitCell.textContent  = formatYen(debit);
        var diff = debit - credit;
        balanceCell.textContent = formatYen(diff);
        var balanced = Math.abs(diff) < 0.0001 && (debit > 0 || credit > 0);
        balanceBar.classList.toggle('is-balanced',   balanced);
        balanceBar.classList.toggle('is-unbalanced', !balanced && (debit > 0 || credit > 0));
        balanceIcon.innerHTML = balanced
          ? '<i class="bi bi-check-circle-fill text-success"></i>'
          : (debit > 0 || credit > 0 ? '<i class="bi bi-exclamation-triangle-fill text-danger"></i>' : '');
        if (submitBtn) {
          submitBtn.classList.toggle('btn-warning', !balanced && (debit > 0 || credit > 0));
          submitBtn.classList.toggle('btn-primary',  balanced || (debit === 0 && credit === 0));
          submitBtn.title = balanced ? '保存します' : '貸借が一致していません — このまま保存も可能ですが警告状態です';
        }
      }

      function buildSubOptions(row, accountId) {
        var subSelect = row.querySelector('select.line-sub');
        var hidden = row.querySelector('input.line-sub-current');
        var current = hidden ? hidden.value : '';
        if (!subSelect) return;
        var subs = (subByAccount && subByAccount[accountId]) || [];
        subSelect.innerHTML = '';
        var blank = document.createElement('option');
        blank.value = '';
        blank.textContent = subs.length === 0 ? '—' : '（指定なし）';
        subSelect.appendChild(blank);
        subs.forEach(function (s) {
          var opt = document.createElement('option');
          opt.value = s.id;
          opt.textContent = s.code + ' ' + s.name;
          if (s.id === current) opt.selected = true;
          subSelect.appendChild(opt);
        });
        subSelect.disabled = subs.length === 0;
      }

      function applyTaxDefault(row, accountId) {
        var def = taxDefaults && taxDefaults[accountId];
        var taxSelect = row.querySelector('select.line-tax-rate');
        if (!taxSelect) return;
        var key = '0|exempt';
        if (def) {
          var rate = parseFloat(def.rate_percent || '0');
          var kind = def.kind || 'exempt';
          if (kind === 'standard') key = '10|standard';
          else if (kind === 'reduced') key = '8|reduced';
          else if (kind === 'nontax') key = '0|nontax';
          else if (rate === 8) key = '8|reduced';
          else if (rate === 10) key = '10|standard';
        }
        // Only override when the user hasn't manually picked something.
        // We treat "still 0|exempt" as "no manual choice".
        if (taxSelect.value === '0|exempt' || taxSelect.value === '') {
          taxSelect.value = key;
          syncTaxFromSelect(row.querySelector('td.cell-tax'));
        }
      }

      function decodeTaxRate(value) {
        var parts = (value || '0|exempt').split('|');
        return { rate: parts[0] || '0', kind: parts[1] || 'exempt' };
      }
      function syncTaxFromSelect(taxCell) {
        if (!taxCell) return;
        var select = taxCell.querySelector('select.line-tax-rate');
        var rateInput = taxCell.querySelector('input.line-tax-rate-input');
        var reducedInput = taxCell.querySelector('input.line-tax-reduced-input');
        if (!select || !rateInput || !reducedInput) return;
        var dec = decodeTaxRate(select.value);
        var rate = parseFloat(dec.rate);
        rateInput.value = rate.toFixed(2);
        reducedInput.value = (dec.kind === 'reduced') ? '1' : '0';
        recomputeTaxForRow(taxCell.closest('tr.line-row'));
      }
      function recomputeTaxForRow(row) {
        if (!row) return;
        var rateInput = row.querySelector('input.line-tax-rate-input');
        var taxAmtInput = row.querySelector('input.line-tax-amount-input');
        var amountInput = row.querySelector('input.line-amount');
        if (!rateInput || !taxAmtInput || !amountInput) return;
        var rate = parseFloat(rateInput.value || '0');
        var amount = parseAmount(amountInput.value);
        var taxMode = (document.querySelector('input[name="tax-mode"]:checked') || { value: 'inclusive' }).value;
        var tax = 0;
        if (rate > 0 && amount > 0) {
          if (taxMode === 'inclusive') tax = amount * rate / (100 + rate);
          else                          tax = amount * rate / 100;
        }
        taxAmtInput.value = (Math.round(tax * 10000) / 10000).toFixed(4);
      }

      // ---- combobox (vanilla JS, replaces <datalist>) ----
      function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
          return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
      }
      // Mirror RomajiAlias::collapseLongVowels + 訓令式→ヘボン so the
      // user can type either ヘボン or 訓令 (shoumouhinhi / shomohinhi /
      // syoumouhinhi / siyoumouhinhi) and still hit the same alias.
      function normalizeRomaji(s) {
        s = (s || '').toLowerCase();
        // 日本式 `siyou` / `siyu` / `siya` → 訓令式 `syou` / `syu` / `sya` 相当に畳む。
        // 同様に `tiyo` / `tya` 系、`ziyo` / `jya` 系も統合してから次段で
        // ヘボン化する。
        s = s.replace(/siy([aiueo])/g, 'sy$1')
             .replace(/tiy([aiueo])/g, 'ty$1')
             .replace(/ziy([aiueo])/g, 'zy$1')
             .replace(/jiy([aiueo])/g, 'jy$1');
        // 訓令式 → ヘボン (longer first to avoid stepping on each other)
        s = s.replace(/sya/g, 'sha').replace(/syu/g, 'shu').replace(/syo/g, 'sho')
             .replace(/tya/g, 'cha').replace(/tyu/g, 'chu').replace(/tyo/g, 'cho')
             .replace(/jya/g, 'ja').replace(/jyu/g, 'ju').replace(/jyo/g, 'jo')
             .replace(/zya/g, 'ja').replace(/zyu/g, 'ju').replace(/zyo/g, 'jo')
             .replace(/si/g, 'shi')
             .replace(/zi/g, 'ji')
             .replace(/ti/g, 'chi')
             .replace(/tu/g, 'tsu')
             .replace(/hu/g, 'fu');
        // 長音崩し（連続母音 → 1 文字）— ou / uu / oo only (mirrors PHP)
        s = s.replace(/ou+/g, 'o').replace(/uu+/g, 'u').replace(/oo+/g, 'o');
        return s;
      }
      function matchesAccount(account, query) {
        if (!query) return true;
        var q = query.toLowerCase().trim();
        if (q === '') return true;
        // code prefix (raw — codes are like L0001, romaji-normalisation N/A)
        if ((account.code || '').toLowerCase().indexOf(q) === 0) return true;
        // name substring (Japanese — no normalisation needed)
        if ((account.name || '').toLowerCase().indexOf(q) !== -1) return true;
        // romaji substring with both sides normalised so ヘボン/訓令式 and
        // long/short vowel variants all collapse to the same form.
        var qn = normalizeRomaji(q);
        if ((account.romaji || '').indexOf(qn) !== -1) return true;
        return false;
      }
      function renderComboList(listEl, query, activeIdx) {
        // Cap at 200: covers all active titles per entity (master had ~180)
        // while keeping DOM cost predictable. Dropdown scrolls beyond viewport.
        var matches = accountTitles.filter(function (a) { return matchesAccount(a, query); }).slice(0, 200);
        if (matches.length === 0) {
          listEl.innerHTML = '<li class="text-muted small px-2 py-1">候補なし</li>';
          listEl.dataset.matches = '0';
          return [];
        }
        var html = '';
        matches.forEach(function (a, i) {
          // F-4 fix: drop the ledger code from the visible row — the bare
          // account name is what operators expect to scan. The id stays
          // bound through data-account-id; the romaji stays as an aid.
          html += '<li data-account-id="' + escapeHtml(a.id) + '"'
            + (i === activeIdx ? ' class="is-active"' : '')
            + '>'
            + '<span class="ct-name">' + escapeHtml(a.name) + '</span>'
            + (a.romaji ? '<span class="ct-romaji">' + escapeHtml(a.romaji) + '</span>' : '')
            + '</li>';
        });
        listEl.innerHTML = html;
        listEl.dataset.matches = String(matches.length);
        return matches;
      }
      function commitAccount(row, account) {
        var hidden = row.querySelector('input.line-account-id');
        var search = row.querySelector('input.line-account-search');
        if (hidden) hidden.value = account ? account.id : '';
        if (search) search.value = account ? account.name : (search.value || '');
        if (account) {
          buildSubOptions(row, account.id);
          applyTaxDefault(row, account.id);
        }
        recompute();
      }

      function wireCombobox(row) {
        var search = row.querySelector('input.line-account-search');
        var listEl = row.querySelector('ul.combobox-list');
        if (!search || !listEl) return;
        var activeIdx = -1;
        var lastMatches = [];

        function positionList() {
          var rect = search.getBoundingClientRect();
          var listMin = 280;
          var width = Math.max(rect.width, listMin);
          // Keep within viewport horizontally.
          var left = Math.min(rect.left, window.innerWidth - width - 8);
          if (left < 8) left = 8;
          listEl.style.left = left + 'px';
          listEl.style.width = width + 'px';
          // Open downward by default; flip up if not enough room below.
          var spaceBelow = window.innerHeight - rect.bottom;
          var listMax = 280;
          if (spaceBelow < 160 && rect.top > spaceBelow) {
            listEl.style.top = '';
            listEl.style.bottom = (window.innerHeight - rect.top) + 'px';
            listEl.style.maxHeight = Math.min(listMax, rect.top - 8) + 'px';
          } else {
            listEl.style.bottom = '';
            listEl.style.top = rect.bottom + 'px';
            listEl.style.maxHeight = Math.min(listMax, spaceBelow - 8) + 'px';
          }
        }

        function open(showAll) {
          var q = showAll ? '' : (search.value || '');
          activeIdx = -1;
          lastMatches = renderComboList(listEl, q, activeIdx);
          listEl.hidden = false;
          positionList();
        }
        function close() { listEl.hidden = true; }

        // Reposition on viewport changes while the list is visible.
        window.addEventListener('scroll', function () { if (!listEl.hidden) positionList(); }, true);
        window.addEventListener('resize', function () { if (!listEl.hidden) positionList(); });

        search.addEventListener('focus',  function () { open(true); });
        search.addEventListener('input',  function () { open(false); });
        search.addEventListener('keydown', function (e) {
          if (listEl.hidden && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
            open(false);
            e.preventDefault();
            return;
          }
          if (e.key === 'ArrowDown') {
            activeIdx = Math.min(lastMatches.length - 1, activeIdx + 1);
            renderComboList(listEl, search.value || '', activeIdx);
            e.preventDefault();
          } else if (e.key === 'ArrowUp') {
            activeIdx = Math.max(0, activeIdx - 1);
            renderComboList(listEl, search.value || '', activeIdx);
            e.preventDefault();
          } else if (e.key === 'Enter') {
            if (!listEl.hidden && activeIdx >= 0 && lastMatches[activeIdx]) {
              commitAccount(row, lastMatches[activeIdx]);
              close();
              e.preventDefault();
              // Move focus to amount of same row.
              var amt = row.querySelector('input.line-amount');
              if (amt) amt.focus();
            }
          } else if (e.key === 'Escape') {
            close();
          }
        });
        search.addEventListener('blur', function () {
          // Delay close so click on an <li> still fires.
          setTimeout(function () {
            close();
            // If the search has been emptied, clear the bound account.
            var hidden = row.querySelector('input.line-account-id');
            var label = (search.value || '').trim();
            if (label === '' && hidden) hidden.value = '';
          }, 150);
        });
        listEl.addEventListener('mousedown', function (e) {
          var li = e.target.closest('li[data-account-id]');
          if (!li) return;
          var id = li.dataset.accountId;
          var acc = accountById[id];
          if (acc) {
            commitAccount(row, acc);
          }
          close();
          e.preventDefault();
        });
      }

      function wireRow(row) {
        wireCombobox(row);
        var amount = row.querySelector('input.line-amount');
        if (amount) {
          amount.addEventListener('input', function () { recomputeTaxForRow(row); recompute(); });
          amount.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              focusNextRow(row);
            }
          });
        }
        var taxSelect = row.querySelector('select.line-tax-rate');
        if (taxSelect) {
          taxSelect.addEventListener('change', function () { syncTaxFromSelect(row.querySelector('td.cell-tax')); recompute(); });
        }
        var sub = row.querySelector('select.line-sub');
        if (sub) {
          sub.addEventListener('change', function () {
            var hidden = row.querySelector('input.line-sub-current');
            if (hidden) hidden.value = sub.value;
          });
        }
        var del = row.querySelector('.remove-line-btn');
        if (del) {
          del.addEventListener('click', function () {
            var tbody = row.closest('tbody');
            var rowsInside = tbody.querySelectorAll('tr.line-row');
            if (rowsInside.length <= 1) {
              clearRow(row);
            } else {
              row.remove();
              reindex();
            }
            recompute();
          });
        }
      }

      function clearRow(row) {
        row.querySelectorAll('input').forEach(function (el) {
          if (el.classList.contains('line-tax-rate-input'))    { el.value = '0.00'; return; }
          if (el.classList.contains('line-tax-reduced-input')) { el.value = '0';    return; }
          if (el.classList.contains('line-tax-amount-input'))  { el.value = '0.0000'; return; }
          if (el.type === 'hidden' && el.name && el.name.indexOf('[side]') !== -1) return;
          el.value = '';
        });
        row.querySelectorAll('select.line-tax-rate').forEach(function (s) { s.value = '0|exempt'; });
        row.querySelectorAll('select.line-sub').forEach(function (s) { s.innerHTML = '<option value="">—</option>'; s.disabled = true; });
      }

      function focusNextRow(row) {
        var tbody = row.closest('tbody');
        var rows = Array.from(tbody.querySelectorAll('tr.line-row'));
        var idx = rows.indexOf(row);
        var next = (idx === -1 || idx === rows.length - 1) ? addRow(tbody.closest('table').dataset.side) : rows[idx + 1];
        if (next) {
          var s = next.querySelector('input.line-account-search');
          if (s) s.focus();
        }
      }

      function addRow(side, prefill) {
        var tpl = (side === 'credit') ? creditTpl : debitTpl;
        var tbody = (side === 'credit') ? creditTbody : debitTbody;
        if (!tpl || !('content' in tpl) || !tbody) return null;
        var html = tpl.innerHTML.replace(/__GIDX__/g, '0').replace(/__NUM__/g, '0');
        var tmp = document.createElement('tbody');
        tmp.innerHTML = html;
        var newRow = tmp.querySelector('tr.line-row');
        if (!newRow) return null;
        tbody.appendChild(newRow);
        wireRow(newRow);
        if (prefill) applyPrefillToRow(newRow, prefill);
        reindex();
        recompute();
        return newRow;
      }

      function applyPrefillToRow(row, line) {
        var hidden = row.querySelector('input.line-account-id');
        var search = row.querySelector('input.line-account-search');
        if (hidden) hidden.value = line.account_title_id || '';
        var a = accountById[line.account_title_id];
        if (search) search.value = a ? (a.code + ' ' + a.name) : '';
        var subHidden = row.querySelector('input.line-sub-current');
        if (subHidden) subHidden.value = line.sub_account_title_id || '';
        buildSubOptions(row, line.account_title_id || '');
        var amount = row.querySelector('input.line-amount');
        if (amount) amount.value = line.amount || '';
        var memo = row.querySelector('input.line-memo');
        if (memo) memo.value = line.memo || '';
        var taxSelect = row.querySelector('select.line-tax-rate');
        if (taxSelect) {
          var rate = parseFloat(line.tax_rate_percent || '0');
          var key = '0|exempt';
          if (rate === 8 && line.is_tax_reduced) key = '8|reduced';
          else if (rate === 10) key = '10|standard';
          taxSelect.value = key;
          syncTaxFromSelect(row.querySelector('td.cell-tax'));
        }
      }

      // ---- initial wiring of pre-rendered rows ----
      allRows().forEach(function (row) {
        wireRow(row);
        var hidden = row.querySelector('input.line-account-id');
        var search = row.querySelector('input.line-account-search');
        if (hidden && hidden.value) {
          var a = accountById[hidden.value];
          if (a && search && !search.value) search.value = a.code + ' ' + a.name;
          buildSubOptions(row, hidden.value);
        }
        // Honor any pre-selected tax-rate select (e.g. edit form).
        var tax = row.querySelector('select.line-tax-rate');
        if (tax) syncTaxFromSelect(row.querySelector('td.cell-tax'));
      });
      reindex();
      recompute();

      // Add-row buttons (per side)
      document.querySelectorAll('button.side-add-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var side = btn.dataset.side;
          var row = addRow(side);
          if (row) {
            var s = row.querySelector('input.line-account-search');
            if (s) s.focus();
          }
        });
      });

      // tax-mode change → recompute every row
      document.querySelectorAll('input[name="tax-mode"]').forEach(function (el) {
        el.addEventListener('change', function () {
          allRows().forEach(function (r) { recomputeTaxForRow(r); });
          recompute();
        });
      });


      // keyboard shortcuts on the form
      form.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
          e.preventDefault();
          if (submitBtn) submitBtn.click();
          return;
        }
        if (e.key === 'Escape') {
          // Close any visible combobox popups first.
          var openCombos = document.querySelectorAll('ul.combobox-list:not([hidden])');
          if (openCombos.length > 0) {
            openCombos.forEach(function (l) { l.hidden = true; });
            return;
          }
          // Otherwise: clear the last row of the active side.
          var active = document.activeElement;
          if (!active) return;
          var row = active.closest('tr.line-row');
          if (!row) return;
          var tbody = row.closest('tbody');
          var rows = tbody.querySelectorAll('tr.line-row');
          if (rows[rows.length - 1] === row) {
            clearRow(row);
            recompute();
          }
        }
      });
    })();
  </script>
