<nav class="navbar navbar-expand-lg" style="background:#fff;border-bottom:1px solid #e5e7eb;">
  <div class="container-fluid">
    <a class="navbar-brand rucaro-brand" href="/ui/dashboard">
      <i class="bi bi-journal-bookmark-fill text-primary"></i> Rucaro Accounting
    </a>

    {* F-1: navbar selectors render whenever any entity context is in scope.
       Inputs:
         - $entities             — list of entities the user owns (for the
                                   dropdown). Empty/absent => the dropdown
                                   collapses to a hidden input that re-posts
                                   the currently selected id, so the fiscal
                                   term selector still works.
         - $nav_fiscal_terms     — preferred source for the term <select>;
           or $fiscal_terms        controllers that already query terms for
                                   the page (Journal / Report) need no
                                   additional wiring.
         - $selected_entity_id, $selected_fiscal_term[_id] — current state. *}
    {assign var="_nav_entities" value=$entities|default:[]}
    {assign var="_nav_terms" value=$nav_fiscal_terms|default:($fiscal_terms|default:[])}
    {assign var="_sel_entity" value=$selected_entity_id|default:''}
    {assign var="_sel_term" value=$selected_fiscal_term_id|default:($selected_fiscal_term|default:'')}
    {if $_sel_entity != '' || count($_nav_entities) > 0}
    <form class="d-flex align-items-center gap-2 mx-3" method="post" action="/ui/entity/switch">
      <input type="hidden" name="_csrf" value="{$csrf_entity_token|default:''|escape}">
      <label for="nav-entity-select" class="form-label mb-0 small text-muted">事業者</label>
      {if count($_nav_entities) > 0}
        <select id="nav-entity-select" name="entity_id" class="form-select form-select-sm" style="min-width:200px;">
          {foreach $_nav_entities as $e}
            <option value="{$e.id|escape}"{if $_sel_entity == $e.id} selected{/if}>{$e.name|escape}</option>
          {/foreach}
        </select>
      {else}
        {* Page didn't load the entity list (most non-dashboard pages skip it
           to keep the payload small). Re-post the current id so the form
           round-trips a fiscal-term change without unsetting entity. *}
        <input type="hidden" name="entity_id" value="{$_sel_entity|escape}">
      {/if}
      <label for="nav-fiscal-select" class="form-label mb-0 small text-muted">会計期間</label>
      {if count($_nav_terms) > 0}
        <select id="nav-fiscal-select" name="fiscal_term_id" class="form-select form-select-sm" style="min-width:240px;">
          {foreach $_nav_terms as $t}
            {assign var="_label_start" value=$t.startDate|default:''}
            {assign var="_label_end" value=$t.endDate|default:''}
            <option value="{$t.id|escape}"{if $_sel_term != '' && $_sel_term == $t.id} selected{/if}>第 {$t.fiscalPeriod} 期 ({$_label_start|escape} 〜 {$_label_end|escape})</option>
          {/foreach}
        </select>
      {else}
        <input type="hidden" name="fiscal_term_id" value="{$_sel_term|escape}">
        <span class="small text-muted" style="min-width:160px;">期間未登録</span>
      {/if}
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
