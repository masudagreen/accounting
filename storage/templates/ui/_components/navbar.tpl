<nav class="navbar navbar-expand-lg" style="background:#fff;border-bottom:1px solid #e5e7eb;">
  <div class="container-fluid">
    <a class="navbar-brand rucaro-brand" href="/ui/dashboard">
      <i class="bi bi-journal-bookmark-fill text-primary"></i> Rucaro Accounting
    </a>

    {if isset($entities) && count($entities) > 0}
    <form class="d-flex align-items-center gap-2 mx-3" method="post" action="/ui/entity/switch">
      <input type="hidden" name="_csrf" value="{$csrf_entity_token|default:''|escape}">
      <label for="nav-entity-select" class="form-label mb-0 small text-muted">entity 選択</label>
      <select id="nav-entity-select" name="entity_id" class="form-select form-select-sm" style="min-width:200px;">
        {foreach $entities as $e}
          <option value="{$e.id|escape}"{if isset($selected_entity_id) && $selected_entity_id == $e.id} selected{/if}>{$e.name|escape}</option>
        {/foreach}
      </select>
      <label for="nav-fiscal-input" class="form-label mb-0 small text-muted">fiscal_term</label>
      <input id="nav-fiscal-input" name="fiscal_term_id" class="form-control form-control-sm"
             style="width:160px;" value="{$selected_fiscal_term|default:''|escape}" placeholder="term-id">
      <button class="btn btn-sm btn-outline-primary" type="submit">切替</button>
    </form>
    {/if}

    <div class="ms-auto d-flex align-items-center gap-3">
      {if isset($display_name) && $display_name !== ''}
        <span class="small text-muted"><i class="bi bi-person-circle"></i> {$display_name|escape}</span>
        <form method="post" action="/ui/logout" class="d-inline">
          <input type="hidden" name="_csrf" value="{$csrf_logout_token|default:''|escape}">
          <button class="btn btn-sm btn-outline-secondary" type="submit">
            <i class="bi bi-box-arrow-right"></i> ログアウト
          </button>
        </form>
      {/if}
    </div>
  </div>
</nav>
