<?php /* Smarty version 3.1.24, created on 2019-08-08 15:12:34
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public17One.html" */ ?>
<?php
/*%%SmartyHeaderCode:16152504165d4c3be22d7f62_90459247%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9fda4263726034cae9842a791897eb93930a38b' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public17One.html',
      1 => 1560675142,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16152504165d4c3be22d7f62_90459247',
  'variables' => 
  array (
    'str17' => 0,
    'strUnit' => 0,
    'strMonthly' => 0,
    'strTitle' => 0,
    'arrRows' => 0,
    'value' => 0,
    'strSum' => 0,
    'strSumPrev' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d4c3be2393d08_21216562',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d4c3be2393d08_21216562')) {
function content_5d4c3be2393d08_21216562 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '16152504165d4c3be22d7f62_90459247';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn"  colspan=2 style="font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['str17']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strMonthly']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStr" style="width:750px;"><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
		</tr>
		<?php
$_from = $_smarty_tpl->tpl_vars['arrRows']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
			<tr valign="top">
				<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['value']->value['strMonth'];?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{numValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
			</tr>
		<?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
			<tr valign="top">
				<td class="codePluginAccountingLibTableColumn "  ><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}SumOffset"><div id="#{idSelf}Sum" >#{sum}</div></td>
			</tr>
			<tr valign="top">
				<td class="codePluginAccountingLibTableColumn"  ><?php echo $_smarty_tpl->tpl_vars['strSumPrev']->value;?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}SumPrevOffset"><div id="#{idSelf}SumPrev" >#{sumPrev}</div></td>
			</tr>
	</tbody>
</table><?php }
}
?>