{*
  F-4: a single rendered <tr> for the credit / debit table on the
  journal new/edit form.

  Required vars:
    side          : 'credit' | 'debit'
    idx           : 0-based per-side index (display only; lines[] uses a
                    JS-managed global index after reindex())
    line          : { account_title_id, sub_account_title_id, amount, memo,
                      tax_rate_percent, tax_amount, is_tax_reduced }
    can_edit      : bool
    account_titles: list (used to render the visible "code name" label
                    for any pre-bound account on form re-render / edit)

  Note: the `name="lines[N][...]"` index is a placeholder — the JS
  reindex() pass rewrites every row to keep them contiguous after add /
  remove. We start by scoping with the per-side idx so a JS-disabled
  client still has a valid (if not contiguous) form body.
*}
<tr class="line-row" data-side="{$side|escape}">
  <td class="text-center text-muted small line-num">{$idx+1}</td>
  <td class="cell-account">
    <input type="hidden" name="lines[{$side|escape}_{$idx}][side]" value="{$side|escape}">
    <input type="hidden" name="lines[{$side|escape}_{$idx}][account_title_id]"
           value="{$line.account_title_id|escape}" class="line-account-id">
    <div class="combobox" role="combobox">
      <input type="text" class="form-control form-control-sm line-account-search combobox-input"
             value="{foreach $account_titles as $a}{if $a.id == $line.account_title_id}{$a.name|escape}{/if}{/foreach}"
             placeholder="科目を検索 (名前/ローマ字)" autocomplete="off"
             {if !$can_edit}readonly{/if}>
      <ul class="combobox-list" hidden></ul>
    </div>
  </td>
  <td class="cell-sub">
    <select name="lines[{$side|escape}_{$idx}][sub_account_title_id]" class="form-select form-select-sm line-sub" disabled{if !$can_edit} disabled{/if}>
      <option value="">—</option>
    </select>
    <input type="hidden" class="line-sub-current" value="{$line.sub_account_title_id|default:''|escape}">
  </td>
  <td class="cell-amount">
    <input type="text" inputmode="decimal" name="lines[{$side|escape}_{$idx}][amount]"
           value="{$line.amount|escape}"
           class="form-control form-control-sm text-end line-amount"
           {if !$can_edit}readonly{/if}>
  </td>
  <td class="cell-tax">
    <select class="form-select form-select-sm line-tax-rate"{if !$can_edit} disabled{/if}>
      <option value="0|exempt"{if $line.tax_rate_percent == '0' || $line.tax_rate_percent == '0.00'}{if !$line.is_tax_reduced} selected{/if}{/if}>対象外</option>
      <option value="0|nontax">非課税</option>
      <option value="8|reduced"{if ($line.tax_rate_percent == '8' || $line.tax_rate_percent == '8.00') && $line.is_tax_reduced} selected{/if}>軽減 8%</option>
      <option value="10|standard"{if ($line.tax_rate_percent == '10' || $line.tax_rate_percent == '10.00') && !$line.is_tax_reduced} selected{/if}>標準 10%</option>
    </select>
    <input type="hidden" name="lines[{$side|escape}_{$idx}][tax_rate_percent]" value="{$line.tax_rate_percent|escape}" class="line-tax-rate-input">
    <input type="hidden" name="lines[{$side|escape}_{$idx}][tax_amount]" value="{$line.tax_amount|escape}" class="line-tax-amount-input">
    <input type="hidden" name="lines[{$side|escape}_{$idx}][is_tax_reduced]" value="{if $line.is_tax_reduced}1{else}0{/if}" class="line-tax-reduced-input">
  </td>
  <td class="cell-memo">
    <input type="text" name="lines[{$side|escape}_{$idx}][memo]" value="{$line.memo|escape}"
           class="form-control form-control-sm line-memo" maxlength="500"
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
