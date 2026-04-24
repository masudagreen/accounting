{if isset($flash_messages) && count($flash_messages) > 0}
  <div class="mb-3">
  {foreach $flash_messages as $msg}
    {assign var="alert_class" value="alert-info"}
    {if $msg.kind == 'success'}{assign var="alert_class" value="alert-success"}{/if}
    {if $msg.kind == 'error'}{assign var="alert_class" value="alert-danger"}{/if}
    {if $msg.kind == 'warning'}{assign var="alert_class" value="alert-warning"}{/if}
    <div class="alert {$alert_class} alert-dismissible fade show" role="alert">
      {$msg.message|escape}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  {/foreach}
  </div>
{/if}
