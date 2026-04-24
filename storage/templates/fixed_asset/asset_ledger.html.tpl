{extends file="layout.html.tpl"}
{block name="content"}
  {if !$ledger.books}
    <p class="empty-book">登録されている固定資産がありません。</p>
  {else}
    {foreach from=$ledger.books item=book}
      <div class="asset-book">
        <h2>[{$book.asset.assetCode|escape}] {$book.asset.assetName|escape} ({$book.asset.categoryCode|escape})</h2>
        <div class="asset-summary">
          <span><span class="label">取得日:</span> {$book.asset.acquisitionDate|escape}</span>
          <span><span class="label">供用開始:</span> {$book.asset.serviceStartDate|escape}</span>
          {if $book.asset.disposalDate}<span><span class="label">除却日:</span> {$book.asset.disposalDate|escape}</span>{/if}
          <span><span class="label">取得価額:</span> ¥{$book.asset.acquisitionCost|escape}</span>
          <span><span class="label">残存価額:</span> ¥{$book.asset.residualValue|escape}</span>
          <span><span class="label">耐用年数:</span> {$book.asset.usefulLifeYears|escape} 年</span>
          <span><span class="label">償却方法:</span> {$book.asset.method|escape}</span>
        </div>

        {if !$book.schedule}
          <p class="empty-book">減価償却スケジュール未生成です。</p>
        {else}
          <table class="schedule-table">
            <thead>
              <tr>
                <th>期</th>
                <th>期間</th>
                <th>供用月数</th>
                <th>期首簿価</th>
                <th>当期償却</th>
                <th>累計償却額</th>
                <th>期末簿価</th>
                <th>投稿済</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$book.schedule item=entry}
                <tr class="{if $entry.isPosted}posted{/if}">
                  <td class="period">第{$entry.periodNumber|escape}期</td>
                  <td class="date">{$entry.periodStartDate|escape} 〜 {$entry.periodEndDate|escape}</td>
                  <td class="period">{$entry.monthsInService|escape}</td>
                  <td class="amount">{$entry.openingBookValue|escape}</td>
                  <td class="amount">{$entry.depreciationAmount|escape}</td>
                  <td class="amount">{$entry.accumulatedDepreciation|escape}</td>
                  <td class="amount">{$entry.closingBookValue|escape}</td>
                  <td class="period">{if $entry.isPosted}○{else}—{/if}</td>
                </tr>
              {/foreach}
            </tbody>
          </table>
        {/if}
      </div>
    {/foreach}
  {/if}
{/block}
