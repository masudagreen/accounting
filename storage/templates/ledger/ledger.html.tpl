{extends file="layout.html.tpl"}
{block name="content"}
  {if !$ledger.books}
    <p class="empty-book">対象期間に該当する勘定科目がありません。</p>
  {else}
    {foreach from=$ledger.books item=book}
      <div class="ledger-book">
        <h2>[{$book.accountTitleCode|escape}] {$book.accountTitleName|escape}</h2>
        <table class="ledger-table">
          <thead>
            <tr>
              <th>日付</th>
              <th>相手勘定</th>
              <th>摘要</th>
              <th>借方</th>
              <th>貸方</th>
              <th>残高</th>
            </tr>
          </thead>
          <tbody>
            <tr class="opening">
              <td class="date">{$ledger.fromDate|escape}</td>
              <td class="counter">—</td>
              <td>前期繰越</td>
              <td class="amount"></td>
              <td class="amount"></td>
              <td class="amount">{$book.openingBalance|escape}</td>
            </tr>
            {if !$book.entries}
              <tr>
                <td colspan="6" class="empty-book">（対象期間内の仕訳はありません）</td>
              </tr>
            {else}
              {foreach from=$book.entries item=entry}
                <tr>
                  <td class="date">{$entry.entryDate|escape}</td>
                  <td class="counter">
                    {if $entry.counterAccountCode}[{$entry.counterAccountCode|escape}] {/if}{$entry.counterAccountName|escape}
                  </td>
                  <td>
                    {if $entry.summary}{$entry.summary|escape}{/if}
                    {if $entry.memo} / {$entry.memo|escape}{/if}
                  </td>
                  <td class="amount">{$entry.debitAmount|escape}</td>
                  <td class="amount">{$entry.creditAmount|escape}</td>
                  <td class="amount">{$entry.runningBalance|escape}</td>
                </tr>
              {/foreach}
            {/if}
            <tr class="closing">
              <td class="date">{$ledger.toDate|escape}</td>
              <td class="counter">—</td>
              <td>期中合計 / 期末残高</td>
              <td class="amount">{$book.debitTotal|escape}</td>
              <td class="amount">{$book.creditTotal|escape}</td>
              <td class="amount">{$book.closingBalance|escape}</td>
            </tr>
          </tbody>
        </table>
      </div>
    {/foreach}
  {/if}
{/block}
