<?php /* Smarty version 3.1.24, created on 2019-10-06 10:05:04
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balance.html" */ ?>
<?php
/*%%SmartyHeaderCode:20832329245d99bc5021e364_03339785%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea6e0d79ec891084afd7ce218be293bf87e8d295' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balance.html',
      1 => 1570328742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20832329245d99bc5021e364_03339785',
  'variables' => 
  array (
    'strTree' => 0,
    'strBalance' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99bc506eb425_98914546',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99bc506eb425_98914546')) {
function content_5d99bc506eb425_98914546 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '20832329245d99bc5021e364_03339785';
?>
<table id="#{idSelf}TableWrap" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr id="#{idSelf}IdAccountTitleWrap">
			<td class="codeLibBaseTableColumn" style="width:200px;"><?php echo $_smarty_tpl->tpl_vars['strTree']->value;?>
</td>
			<td class="codeLibBaseTableColumn"><?php echo $_smarty_tpl->tpl_vars['strBalance']->value;?>
</td>
		</tr>
		#{strForm}
	</tbody>
</table>
<?php }
}
?>