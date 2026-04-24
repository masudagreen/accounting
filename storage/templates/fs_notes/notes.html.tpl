{extends file="layout.html.tpl"}

{block name="header"}
  <div class="meta">
    <span>EntityID: {$entityId|escape}</span>
    <span>FiscalTermID: {$fiscalTermId|escape}</span>
    <span>生成日時: {$generatedAt|escape}</span>
  </div>
{/block}

{block name="content"}
  {if !$sections}
    <div class="empty">登録されている注記がありません。</div>
  {else}
    {foreach from=$sections item=section}
      <section>
        <h2>{$section.categoryLabel|escape}</h2>
        {foreach from=$section.items item=note}
          <div class="note-item">
            <div class="note-label">
              {$note.label|escape}
              {if $note.templateCode}<span class="tpl">[{$note.templateCode|escape}]</span>{/if}
            </div>
            <div class="note-body">{$note.body|escape}</div>
          </div>
        {/foreach}
      </section>
    {/foreach}
  {/if}
{/block}
