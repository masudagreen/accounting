{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">勘定科目 × 消費税区分</h1>
      <p class="text-muted mb-0">各勘定科目の既定の消費税区分と税率コードを設定します。未設定は空欄のままで構いません。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/consumption-tax/periods"><i class="bi bi-arrow-left"></i> 消費税メニューへ戻る</a>
    </div>
  </header>

  <form method="post" action="/ui/consumption-tax/account-defaults" class="rucaro-card shadow-sm">
    <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>コード</th>
            <th>勘定科目名</th>
            <th>区分</th>
            <th style="width: 180px">税率コード</th>
          </tr>
        </thead>
        <tbody>
          {foreach $items as $idx => $a}
            <tr>
              <td><code>{$a.code|escape}</code></td>
              <td>
                {$a.name|escape}
                <input type="hidden" name="rows[{$idx}][account_title_id]" value="{$a.id|escape}">
              </td>
              <td>
                <select name="rows[{$idx}][category_code]" class="form-select form-select-sm">
                  {foreach $category_options as $opt}
                    <option value="{$opt.value|escape}"{if $a.category == $opt.value} selected{/if}>{$opt.label|escape}</option>
                  {/foreach}
                </select>
              </td>
              <td>
                <input type="text" name="rows[{$idx}][rate_code]" value="{$a.rate|escape}" class="form-control form-control-sm" placeholder="例: standard_10">
              </td>
            </tr>
          {foreachelse}
            <tr><td colspan="4" class="text-center text-muted py-3">勘定科目がありません。</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
    <div class="p-3 border-top text-end">
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 一括保存</button>
    </div>
  </form>
{/block}
