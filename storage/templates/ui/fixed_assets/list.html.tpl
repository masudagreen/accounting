{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">固定資産</h1>
      <p class="text-muted mb-0">事業者の固定資産台帳を一覧します。除却済みの表示はチェックで切替できます。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/fixed-assets/new">
        <i class="bi bi-plus-lg"></i> 新規固定資産
      </a>
    </div>
  </header>

  <section class="rucaro-card p-3 mb-3 shadow-sm">
    <form method="get" action="/ui/fixed-assets" class="d-flex align-items-center gap-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="true" name="includeDisposed" id="includeDisposed"
               {if $include_disposed}checked{/if}>
        <label class="form-check-label" for="includeDisposed">除却済みを含める</label>
      </div>
      <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-funnel"></i> 表示を更新
      </button>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>資産コード</th>
            <th>資産名</th>
            <th>区分</th>
            <th>取得日</th>
            <th class="text-end">取得原価</th>
            <th class="text-end">耐用年数</th>
            <th>償却方法</th>
            <th>状態</th>
            <th class="text-end" style="min-width: 100px;">操作</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="9" class="text-center text-muted py-5">
                登録された固定資産はありません。<br>
                <a class="btn btn-sm btn-outline-primary mt-3" href="/ui/fixed-assets/new">
                  <i class="bi bi-plus-lg"></i> 新規固定資産を登録
                </a>
              </td>
            </tr>
          {else}
            {foreach $items as $a}
              <tr>
                <td><code>{$a.assetCode|escape}</code></td>
                <td><a class="text-decoration-none" href="/ui/fixed-assets/{$a.id|escape}">{$a.assetName|escape}</a></td>
                <td>{$a.categoryCode|escape}</td>
                <td><code>{$a.acquisitionDate|escape}</code></td>
                <td class="text-end">{$a.acquisitionCost|escape}</td>
                <td class="text-end">{$a.usefulLifeYears}年</td>
                <td><small class="text-muted">{$a.method|escape}</small></td>
                <td>
                  {if $a.isDisposed}
                    <span class="badge text-bg-secondary">除却済 ({$a.disposalDate|escape})</span>
                  {else}
                    <span class="badge text-bg-success">稼働中</span>
                  {/if}
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/fixed-assets/{$a.id|escape}">詳細</a>
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
    <div class="p-3 text-end small text-muted border-top">全 {$total} 件</div>
  </section>
{/block}
