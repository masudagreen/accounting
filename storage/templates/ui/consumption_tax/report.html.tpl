{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">消費税申告書</h1>
      <p class="text-muted mb-0">期間 <code>{$settlement.periodFrom|escape}</code> 〜 <code>{$settlement.periodTo|escape}</code> ({$settlement.methodLabel|escape})</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/consumption-tax/periods/{$settlement.periodId|escape}">
        <i class="bi bi-arrow-left"></i> 詳細へ戻る
      </a>
    </div>
  </header>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <h2 class="h5 mb-3">売上区分別</h2>
    <dl class="row g-2 mb-0">
      <dt class="col-sm-3 text-muted">課税売上</dt>
      <dd class="col-sm-3 text-end">{$settlement.taxableSales|escape}</dd>
      <dt class="col-sm-3 text-muted">非課税売上</dt>
      <dd class="col-sm-3 text-end">{$settlement.nonTaxableSales|escape}</dd>
      <dt class="col-sm-3 text-muted">免税売上</dt>
      <dd class="col-sm-3 text-end">{$settlement.exemptSales|escape}</dd>
      <dt class="col-sm-3 text-muted">不課税売上</dt>
      <dd class="col-sm-3 text-end">{$settlement.untaxedSales|escape}</dd>
      <dt class="col-sm-3 text-muted">売上合計</dt>
      <dd class="col-sm-3 text-end"><strong>{$settlement.totalSales|escape}</strong></dd>
      <dt class="col-sm-3 text-muted">課税売上割合</dt>
      <dd class="col-sm-3 text-end">{$settlement.taxableSalesRatio|escape}</dd>
    </dl>
  </section>

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <h2 class="h5 mb-3">税額</h2>
    <dl class="row g-2 mb-0">
      <dt class="col-sm-3 text-muted">仮受消費税</dt>
      <dd class="col-sm-3 text-end">{$settlement.outputTax|escape}</dd>
      <dt class="col-sm-3 text-muted">仕入税額控除</dt>
      <dd class="col-sm-3 text-end">{$settlement.deductibleInputTax|escape}</dd>
      <dt class="col-sm-3 text-muted">未登録事業者調整</dt>
      <dd class="col-sm-3 text-end">{$settlement.adjustmentForNonRegistered|escape}</dd>
      <dt class="col-sm-3 text-muted">納付税額</dt>
      <dd class="col-sm-3 text-end"><strong class="fs-5">{$settlement.netTaxPayable|escape}</strong></dd>
      <dt class="col-sm-3 text-muted">うち国税</dt>
      <dd class="col-sm-3 text-end">{$settlement.national|escape}</dd>
      <dt class="col-sm-3 text-muted">うち地方税</dt>
      <dd class="col-sm-3 text-end">{$settlement.local|escape}</dd>
    </dl>
  </section>

  <section class="rucaro-card p-4 shadow-sm">
    <h2 class="h5 mb-3">税率別内訳</h2>
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>税率</th>
            <th class="text-end">課税売上</th>
            <th class="text-end">仮受消費税</th>
            <th class="text-end">課税仕入</th>
            <th class="text-end">仮払消費税</th>
          </tr>
        </thead>
        <tbody>
          {foreach $settlement.salesByRate as $rate => $amount}
            <tr>
              <td>{$rate|escape}</td>
              <td class="text-end">{$amount|escape}</td>
              <td class="text-end">{$settlement.outputTaxByRate[$rate]|default:'0.0000'|escape}</td>
              <td class="text-end">{$settlement.purchasesByRate[$rate]|default:'0.0000'|escape}</td>
              <td class="text-end">{$settlement.inputTaxByRate[$rate]|default:'0.0000'|escape}</td>
            </tr>
          {foreachelse}
            <tr><td colspan="5" class="text-center text-muted py-3">税率別データなし</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </section>
{/block}
