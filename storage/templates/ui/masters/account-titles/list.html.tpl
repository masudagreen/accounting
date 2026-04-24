{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">勘定科目マスタ</h1>
      <p class="text-muted mb-0">選択中の事業者の勘定科目を分類ごとに管理します。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/masters/account-titles/new">
        <i class="bi bi-plus-lg"></i> 新規追加
      </a>
    </div>
  </header>

  <section class="rucaro-card shadow-sm mb-4">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <span class="text-muted small">全 {$total} 件</span>
    </div>
    {foreach $categories as $cat}
      <div class="px-3 pt-3">
        <h2 class="h6 mb-2 text-muted">
          {$category_labels[$cat]|default:$cat|escape}
          <span class="badge text-bg-light ms-2">{count($grouped[$cat])} 件</span>
        </h2>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width: 8rem">コード</th>
              <th scope="col">名称</th>
              <th scope="col" style="width: 6rem">貸借</th>
              <th scope="col" style="width: 6rem" class="text-end">並び</th>
              <th scope="col" style="width: 5rem">有効</th>
              <th scope="col" class="text-end" style="width: 16rem">アクション</th>
            </tr>
          </thead>
          <tbody>
            {if count($grouped[$cat]) == 0}
              <tr><td colspan="6" class="text-center text-muted py-3">登録がありません。</td></tr>
            {else}
              {foreach $grouped[$cat] as $row}
                <tr>
                  <td><code>{$row.code|escape}</code></td>
                  <td>{$row.name|escape}</td>
                  <td><span class="badge text-bg-{if $row.normalSide == 'debit'}primary{else}info{/if}">{$normal_side_labels[$row.normalSide]|default:$row.normalSide|escape}</span></td>
                  <td class="text-end"><span class="text-muted">{$row.sortOrder}</span></td>
                  <td>{if $row.isActive}<span class="badge text-bg-success">有効</span>{else}<span class="badge text-bg-secondary">無効</span>{/if}</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="/ui/masters/account-titles/{$row.id|escape}">編集</a>
                    <form method="post" action="/ui/masters/account-titles/{$row.id|escape}/delete" class="d-inline" onsubmit="return confirm('本当に削除しますか？');">
                      <input type="hidden" name="_csrf" value="{$csrf_delete_token|escape}">
                      <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                    </form>
                  </td>
                </tr>
              {/foreach}
            {/if}
          </tbody>
        </table>
      </div>
    {/foreach}
  </section>
{/block}
