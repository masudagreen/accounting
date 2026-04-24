{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">総勘定元帳</h1>
      <p class="text-muted mb-0 small">
        期間: <code>{$from_date|escape}</code> 〜 <code>{$to_date|escape}</code>
        {if $term_start !== '' || $term_end !== ''}
          （会計期: {$term_start|escape} 〜 {$term_end|escape}）
        {/if}
      </p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm"
         href="/ui/ledger?format=pdf{if $selected_account_title_id !== ''}&accountTitleId={$selected_account_title_id|escape}{/if}{if $year !== ''}&year={$year|escape}{/if}{if $month !== ''}&month={$month|escape}{/if}">
        <i class="bi bi-file-earmark-pdf"></i> PDF ダウンロード
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 shadow-sm mb-4">
    <form method="get" action="/ui/ledger" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label for="ledger-account-title" class="form-label small text-muted mb-1">勘定科目</label>
        <select id="ledger-account-title" name="accountTitleId" class="form-select form-select-sm">
          <option value="">全勘定</option>
          {foreach $account_titles as $a}
            <option value="{$a.id|escape}"{if $selected_account_title_id == $a.id} selected{/if}>[{$a.code|escape}] {$a.name|escape}</option>
          {/foreach}
        </select>
      </div>
      <div class="col-md-2">
        <label for="ledger-year" class="form-label small text-muted mb-1">年</label>
        <input id="ledger-year" name="year" class="form-control form-control-sm" value="{$year|escape}" placeholder="2025">
      </div>
      <div class="col-md-2">
        <label for="ledger-month" class="form-label small text-muted mb-1">月 (1-12)</label>
        <input id="ledger-month" name="month" class="form-control form-control-sm" value="{$month|escape}" placeholder="12">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-funnel"></i> 絞り込み
        </button>
        <a href="/ui/ledger" class="btn btn-outline-secondary btn-sm">リセット</a>
      </div>
    </form>
  </section>

  {if count($books) == 0}
    <div class="alert alert-info">この期間に合致する元帳はありません。</div>
  {else}
    {foreach $books as $book}
      <section class="rucaro-card p-3 shadow-sm mb-4">
        <h2 class="h5 mb-2">
          <i class="bi bi-list-columns-reverse text-primary"></i>
          [{$book.accountTitleCode|escape}] {$book.accountTitleName|escape}
        </h2>
        <p class="small text-muted mb-2">
          前期繰越 (期首残高): <strong>{$book.openingBalance|escape}</strong>
        </p>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th scope="col" style="width:110px;">日付</th>
                <th scope="col">相手勘定</th>
                <th scope="col">摘要</th>
                <th scope="col" class="text-end">借方</th>
                <th scope="col" class="text-end">貸方</th>
                <th scope="col" class="text-end">残高</th>
              </tr>
            </thead>
            <tbody>
              {foreach $book.entries as $e}
                <tr>
                  <td><code>{$e.entryDate|escape}</code></td>
                  <td><span class="small text-muted">{$e.counterAccountCode|escape}</span> {$e.counterAccountName|escape}</td>
                  <td>{$e.summary|escape}{if $e.memo !== ''} <span class="small text-muted">／ {$e.memo|escape}</span>{/if}</td>
                  <td class="text-end">{$e.debitAmount|escape}</td>
                  <td class="text-end">{$e.creditAmount|escape}</td>
                  <td class="text-end">{$e.runningBalance|escape}</td>
                </tr>
              {/foreach}
            </tbody>
            <tfoot>
              <tr class="fw-semibold">
                <td colspan="3" class="text-end">期中合計</td>
                <td class="text-end">{$book.debitTotal|escape}</td>
                <td class="text-end">{$book.creditTotal|escape}</td>
                <td class="text-end">—</td>
              </tr>
              <tr class="fw-semibold table-secondary">
                <td colspan="5" class="text-end">期末残高</td>
                <td class="text-end">{$book.closingBalance|escape}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </section>
    {/foreach}
  {/if}
{/block}
