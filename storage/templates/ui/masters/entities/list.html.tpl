{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">事業主マスタ</h1>
      <p class="text-muted mb-0">ログイン中のユーザーに紐づく会計主体（個人事業主 / 法人）の一覧です。</p>
    </div>
    <div>
      <a class="btn btn-primary" href="/ui/masters/entities/new">
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
            <th scope="col">屋号 / 会社名</th>
            <th scope="col" style="width: 7rem">区分</th>
            <th scope="col" style="width: 6rem">国</th>
            <th scope="col" style="width: 7rem">通貨</th>
            <th scope="col" style="width: 8rem">決算月日</th>
            <th scope="col" style="width: 5rem">有効</th>
            <th scope="col" class="text-end" style="width: 16rem">アクション</th>
          </tr>
        </thead>
        <tbody>
          {if count($rows) == 0}
            <tr><td colspan="7" class="text-center text-muted py-5">登録がありません。<br><a class="btn btn-sm btn-outline-primary mt-3" href="/ui/masters/entities/new"><i class="bi bi-plus-lg"></i> 新規追加</a></td></tr>
          {else}
            {foreach $rows as $row}
              <tr>
                <td>{$row.name|escape}</td>
                <td>{if $row.isCorporate}<span class="badge text-bg-dark">法人</span>{else}<span class="badge text-bg-primary">個人</span>{/if}</td>
                <td><code>{$row.nationCode|escape}</code></td>
                <td><code>{$row.currencyCode|escape}</code></td>
                <td><code>{$row.fiscalStartMmDd|escape}</code></td>
                <td>{if $row.isActive}<span class="badge text-bg-success">有効</span>{else}<span class="badge text-bg-secondary">無効</span>{/if}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="/ui/masters/entities/{$row.id|escape}">編集</a>
                  <a class="btn btn-sm btn-outline-danger" href="/ui/masters/entities/{$row.id|escape}/delete">削除</a>
                </td>
              </tr>
            {/foreach}
          {/if}
        </tbody>
      </table>
    </div>
  </section>
{/block}
