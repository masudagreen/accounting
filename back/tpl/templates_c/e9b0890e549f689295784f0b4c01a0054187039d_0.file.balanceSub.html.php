<?php /* Smarty version 3.1.24, created on 2019-10-06 10:05:04
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceSub.html" */ ?>
<?php
/*%%SmartyHeaderCode:5394918845d99bc5083b142_62359382%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9b0890e549f689295784f0b4c01a0054187039d' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceSub.html',
      1 => 1570328742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5394918845d99bc5083b142_62359382',
  'variables' => 
  array (
    'strTree' => 0,
    'strBalance' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99bc5085aea7_10118907',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99bc5085aea7_10118907')) {
function content_5d99bc5085aea7_10118907 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5394918845d99bc5083b142_62359382';
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