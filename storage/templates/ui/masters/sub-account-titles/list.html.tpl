{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">補助科目マスタ</h1>
      <p class="text-muted mb-0">各勘定科目に紐づく補助科目の一覧です。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/masters/sub-account-titles/new">
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
            <th scope="col">親勘定科目</th>
            <th scope="col" style="width: 8rem">コード</th>
            <th scope="col">名称</th>
            <th scope="col" style="width: 6rem" class="text-end">並び</th>
            <th scope="col" style="width: 5rem">有効</th>
            <th scope="col" class="text-end" style="width: 16rem">アクション</th>
          </tr>
        </thead>
        <tbody>
          {if count($rows) == 0}
            <tr><td colspan="6" class="text-center text-muted py-5">登録がありません。<br><a class="btn btn-sm btn-outline-primary mt-3" href="/ui/masters/sub-account-titles/new"><i class="bi bi-plus-lg"></i> 新規追加</a></td></tr>
          {else}
            {foreach $rows as $row}
              <tr>
                <td>
                  {if isset($title_map[$row.accountTitleId])}
                    <code>{$title_map[$row.accountTitleId].code|escape}</code>
                    <span class="text-muted">— {$title_map[$row.accountTitleId].name|escape}</span>
                  {else}
                    <span class="text-muted">—</span>
                  {/if}
                </td>
                <td><code>{$row.code|escape}</code></td>
                <td>{$row.name|escape}</td>
                <td class="text-end"><span class="text-muted">{$row.sortOrder}</span></td>
                <td>{if $row.isActive}<span class="badge text-bg-success">有効</span>{else}<span class="badge text-bg-secondary">無効</span>{/if}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/masters/sub-account-titles/{$row.id|escape}">編集</a>
                  <a class="btn btn-sm btn-outline-danger" href="/ui/masters/sub-account-titles/{$row.id|escape}/delete">削除</a>
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
  </section>
{/block}
