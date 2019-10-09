<?php /* Smarty version 3.1.24, created on 2019-08-08 15:12:29
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public17Two.html" */ ?>
<?php
/*%%SmartyHeaderCode:6006905845d4c3bddab6086_61296281%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8e5b5738d46bac1237af1889de96be5052a4aa79' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public17Two.html',
      1 => 1560675142,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6006905845d4c3bddab6086_61296281',
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
  'unifunc' => 'content_5d4c3bddb9db39_77003859',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d4c3bddb9db39_77003859')) {
function content_5d4c3bddb9db39_77003859 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '6006905845d4c3bddab6086_61296281';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn"  colspan=3 style="font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['str17']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn"  rowspan=2><?php echo $_smarty_tpl->tpl_vars['strMonthly']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn"  colspan=2 style="width:750px;"><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
		</tr>
		<tr valign="top">
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}StrTitle1Offset"><div id="#{idSelf}StrTitle1">#{strTitle1}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}StrTitle2Offset"><div id="#{idSelf}StrTitle2"  >#{strTitle2}</div></td>
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
				<td class="codePluginAccountingLibTableColumn"  ><?php echo $_smarty_tpl->tpl_vars['value']->value['strMonth'];?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}1NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}1NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"  >#{1NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}2NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}2NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"  >#{2NumValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
			</tr>
		<?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
			<tr valign="top">
				<td class="codePluginAccountingLibTableColumn"  ><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}Sum1Offset"><div id="#{idSelf}Sum1"  >#{sum1}</div></td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}Sum2Offset"><div id="#{idSelf}Sum2"  >#{sum2}</div></td>
			</tr>
			<tr valign="top">
				<td class="codePluginAccountingLibTableColumn"  ><?php echo $_smarty_tpl->tpl_vars['strSumPrev']->value;?>
</td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}SumPrev1Offset"><div id="#{idSelf}SumPrev1"  >#{sumPrev1}</div></td>
				<td class="codePluginAccountingLibTableRowNum" id="#{idSelf}SumPrev2Offset"><div id="#{idSelf}SumPrev2"  >#{sumPrev2}</div></td>
			</tr>
	</tbody>
</table><?php }
}
?>