{extends file="layout.html.tpl"}

{block name="content"}
  <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">
        {if $form_mode == 'new'}新規仕訳{else}仕訳編集{/if}
      </h1>
      <p class="text-muted mb-0">借方（左）と貸方（右）をそれぞれの表に入力します。合計が一致すると保存できます。</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <button type="button" class="btn btn-outline-secondary btn-sm" id="shortcut-help-btn"
              data-bs-toggle="modal" data-bs-target="#shortcut-help-modal"
              title="キーボードショートカット (?)">
        <i class="bi bi-keyboard"></i> ショートカット
      </button>
      <a class="btn btn-outline-secondary btn-sm" href="/ui/journals">
        <i class="bi bi-arrow-left"></i> 一覧へ戻る
      </a>
    </div>
  </header>

  {include file="journals/_form_body.tpl"}
{/block}
