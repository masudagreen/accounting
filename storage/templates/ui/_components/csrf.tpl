{* CSRF hidden field helper. Usage: {include file="_components/csrf.tpl" token=$token} *}
<input type="hidden" name="_csrf" value="{$token|default:''|escape}">
