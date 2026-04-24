{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">{$plan.name|escape}</h1>
      <p class="text-muted mb-0">資金繰り計画の詳細（通貨: {$plan.currency|escape}）</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/cash-plans"><i class="bi bi-arrow-left"></i> 一覧へ戻る</a>
    </div>
  </header>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <dl class="row g-2 mb-0">
      <dt class="col-sm-2 text-muted">期首残高</dt>
      <dd class="col-sm-4 text-end">{$plan.openingBalance|escape}</dd>
      <dt class="col-sm-2 text-muted">期末残高</dt>
      <dd class="col-sm-4 text-end"><strong>{$running_balances[11]|escape}</strong></dd>
      <dt class="col-sm-2 text-muted">メモ</dt>
      <dd class="col-sm-10">{$plan.notes|default:'（なし）'|escape}</dd>
      <dt class="col-sm-2 text-muted">更新日時</dt>
      <dd class="col-sm-10">{$plan.updatedAt|escape}</dd>
    </dl>
  </section>

  <section class="rucaro-card shadow-sm mb-4">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>区分</th>
            <th>ラベル</th>
            {section name=m start=1 loop=13 step=1}<th class="text-end">{$smarty.section.m.index}月</th>{/section}
            <th class="text-end">合計</th>
          </tr>
        </thead>
        <tbody>
          {foreach $plan.entries as $e}
            <tr>
              <td><small>{$e.category|escape}</small></td>
              <td>{$e.label|escape}</td>
              {section name=m start=0 loop=12 step=1}<td class="text-end">{$e.monthly[$smarty.section.m.index]|escape}</td>{/section}
              <td class="text-end"><strong>{$e.total|escape}</strong></td>
            </tr>
          {foreachelse}
            <tr><td colspan="15" class="text-center text-muted py-4">明細行はありません。</td></tr>
          {/foreach}
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="2">月次差額</th>
            {foreach $monthly_deltas as $d}<th class="text-end">{$d|escape}</th>{/foreach}
            <th></th>
          </tr>
          <tr>
            <th colspan="2">月末残高</th>
            {foreach $running_balances as $rb}<th class="text-end">{$rb|escape}</th>{/foreach}
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </section>

  <section class="rucaro-card p-4 shadow-sm border-danger">
    <h2 class="h5 mb-2 text-danger">削除</h2>
    <form method="post" action="/ui/cash-plans/{$plan.id|escape}/delete"
          onsubmit="return confirm('この資金繰り計画を削除しますか？');" class="text-end">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> 削除</button>
    </form>
  </section>
{/block}
