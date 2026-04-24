{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">インボイス登録事業者</h1>
      <p class="text-muted mb-0">取引先ごとの適格請求書発行事業者 (インボイス) 登録状況を管理します。</p>
    </div>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/consumption-tax/periods"><i class="bi bi-arrow-left"></i> 消費税メニューへ戻る</a>
    </div>
  </header>

  {if isset($form_errors['_'])}
    <div class="alert alert-danger">{foreach $form_errors['_'] as $m}<div>{$m|escape}</div>{/foreach}</div>
  {/if}

  <section class="rucaro-card p-4 shadow-sm mb-4">
    <h2 class="h5 mb-3">新規登録 / 編集</h2>
    <form method="post" action="/ui/consumption-tax/invoice-registrations" class="row g-3">
      <input type="hidden" name="_csrf" value="{$csrf_form_token|escape}">
      <input type="hidden" name="id" value="">
      <div class="col-md-5">
        <label class="form-label">相手先名</label>
        <input type="text" name="counterparty_name" class="form-control{if isset($form_errors.counterparty_name)} is-invalid{/if}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">登録番号 (Tから始まる 14 文字)</label>
        <input type="text" name="registration_number" pattern="T[0-9]{literal}{13}{/literal}" class="form-control" placeholder="T1234567890123">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" value="1" id="is_registered" name="is_registered" checked>
          <label class="form-check-label" for="is_registered">登録済み</label>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label">登録開始日</label>
        <input type="date" name="registered_from" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">登録終了日</label>
        <input type="date" name="registered_until" class="form-control">
      </div>
      <div class="col-md-12">
        <label class="form-label">メモ</label>
        <textarea name="notes" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 保存</button>
      </div>
    </form>
  </section>

  <section class="rucaro-card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>相手先名</th>
            <th>登録番号</th>
            <th>登録状況</th>
            <th>登録期間</th>
            <th>メモ</th>
          </tr>
        </thead>
        <tbody>
          {foreach $items as $r}
            <tr>
              <td>{$r.counterpartyName|escape}</td>
              <td><code>{$r.registrationNumber|escape}</code></td>
              <td>
                {if $r.isRegistered}<span class="badge text-bg-success">登録済</span>
                {else}<span class="badge text-bg-secondary">未登録</span>{/if}
              </td>
              <td><small>{$r.registeredFrom|escape} 〜 {$r.registeredUntil|escape}</small></td>
              <td><small class="text-muted">{$r.notes|escape}</small></td>
            </tr>
          {foreachelse}
            <tr><td colspan="5" class="text-center text-muted py-4">登録情報はありません。</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </section>
{/block}
