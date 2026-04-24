{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">会計期マスタ</h1>
      <p class="text-muted mb-0">選択中の事業者の会計期（期番号と期間）を管理します。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/masters/fiscal-terms/new">
        <i class="bi bi-plus-lg"></i> 新規追加
      </a>
    </div>
  </header>

  <section class="rucaro-card shadow-sm">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <span class="text-muted small">全 {$total} 件</span>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th scope="col" style="width: 8rem" class="text-end">期番号</th>
            <th scope="col">開始日</th>
            <th scope="col">終了日</th>
            <th scope="col" style="width: 7rem">状態</th>
            <th scope="col" class="text-end" style="width: 16rem">アクション</th>
          </tr>
        </thead>
        <tbody>
          {if count($rows) == 0}
            <tr><td colspan="5" class="text-center text-muted py-5">登録がありません。<br><a class="btn btn-sm btn-outline-primary mt-3" href="/ui/masters/fiscal-terms/new"><i class="bi bi-plus-lg"></i> 新規追加</a></td></tr>
          {else}
            {foreach $rows as $row}
              <tr>
                <td class="text-end"><strong>第 {$row.fiscalPeriod} 期</strong></td>
                <td><code>{$row.startDate|escape}</code></td>
                <td><code>{$row.endDate|escape}</code></td>
                <td>{if $row.isClosed}<span class="badge text-bg-secondary">締切済</span>{else}<span class="badge text-bg-success">オープン</span>{/if}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/masters/fiscal-terms/{$row.id|escape}">編集</a>
                  <a class="btn btn-sm btn-outline-danger" href="/ui/masters/fiscal-terms/{$row.id|escape}/delete">削除</a>
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
  </section>
{/block}
