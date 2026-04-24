{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">消費税申告期間</h1>
      <p class="text-muted mb-0">課税期間 (原則課税・簡易課税・2 割特例) を一覧します。</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="/ui/consumption-tax/account-defaults">勘定科目×区分</a>
      <a class="btn btn-outline-secondary" href="/ui/consumption-tax/invoice-registrations">インボイス登録</a>
      <a class="btn btn-primary" href="/ui/consumption-tax/periods/new">
        <i class="bi bi-plus-lg"></i> 新規申告期間
      </a>
    </div>
  </header>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>期間開始</th>
            <th>期間終了</th>
            <th>課税方式</th>
            <th>事業区分</th>
            <th>中間</th>
            <th>ステータス</th>
            <th class="text-end" style="min-width: 140px;">操作</th>
          </tr>
        </thead>
        <tbody>
          {if count($items) == 0}
            <tr>
              <td colspan="7" class="text-center text-muted py-5">
                登録された申告期間はありません。<br>
                <a class="btn btn-sm btn-outline-primary mt-3" href="/ui/consumption-tax/periods/new">
                  <i class="bi bi-plus-lg"></i> 新規申告期間を登録
                </a>
              </td>
            </tr>
          {else}
            {foreach $items as $p}
              <tr>
                <td><code>{$p.periodFrom|escape}</code></td>
                <td><code>{$p.periodTo|escape}</code></td>
                <td>{$p.methodLabel|escape}</td>
                <td>{$p.category|escape}</td>
                <td>{if $p.isInterim}<span class="badge text-bg-warning">中間</span>{else}—{/if}</td>
                <td><span class="badge text-bg-secondary">{$p.status|escape}</span></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/consumption-tax/periods/{$p.id|escape}">詳細</a>
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
