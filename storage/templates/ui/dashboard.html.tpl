{extends file="layout.html.tpl"}

{block name="content"}
  {assign var="active_nav" value="dashboard"}
  {assign var="page_title" value="ダッシュボード"}

  <header class="mb-4">
    <h1 class="h3 mb-1">ダッシュボード</h1>
    <p class="text-muted mb-0">
      ようこそ、{$display_name|default:'ユーザ'|escape} さん。
      {if $selected_entity_id !== ''}選択中の entity: <code>{$selected_entity_id|escape}</code>{else}entity が未選択です。上部ナビから選択してください。{/if}
    </p>
  </header>

  <section class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
      <a href="/ui/journals" class="text-decoration-none text-reset">
        <div class="rucaro-card rucaro-kpi h-100 shadow-sm">
          <span class="label"><i class="bi bi-journal-text text-primary"></i> 仕訳一覧</span>
          <span class="value">Phase 7-2</span>
          <span class="small text-muted">入力 / 編集 / 承認</span>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="/ui/ledger" class="text-decoration-none text-reset">
        <div class="rucaro-card rucaro-kpi h-100 shadow-sm">
          <span class="label"><i class="bi bi-list-columns-reverse text-primary"></i> 総勘定元帳</span>
          <span class="value">Phase 7-2</span>
          <span class="small text-muted">勘定別 T/B 閲覧</span>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="/ui/pl" class="text-decoration-none text-reset">
        <div class="rucaro-card rucaro-kpi h-100 shadow-sm">
          <span class="label"><i class="bi bi-graph-up-arrow text-primary"></i> 損益計算書</span>
          <span class="value">Phase 7-3</span>
          <span class="small text-muted">当期純利益まで</span>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="/ui/bs" class="text-decoration-none text-reset">
        <div class="rucaro-card rucaro-kpi h-100 shadow-sm">
          <span class="label"><i class="bi bi-columns-gap text-primary"></i> 貸借対照表</span>
          <span class="value">Phase 7-3</span>
          <span class="small text-muted">資産 = 負債 + 純資産</span>
        </div>
      </a>
    </div>
  </section>

  <section class="rucaro-card p-3 shadow-sm">
    <h2 class="h5 mb-3"><i class="bi bi-clock-history"></i> 最近の仕訳</h2>
    {if count($recent_journals) == 0}
      <p class="text-muted mb-0">最近の仕訳はまだありません。</p>
    {else}
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th scope="col">日付</th>
              <th scope="col">摘要</th>
              <th scope="col" class="text-end">金額</th>
              <th scope="col">状態</th>
            </tr>
          </thead>
          <tbody>
            {foreach $recent_journals as $j}
              <tr>
                <td><code>{$j.journalDate|escape}</code></td>
                <td>{$j.summary|escape}</td>
                <td class="text-end">{$j.totalAmount|escape}</td>
                <td><span class="badge text-bg-secondary">{$j.status|escape}</span></td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    {/if}
  </section>
{/block}
