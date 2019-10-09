<?php /* Smarty version 3.1.24, created on 2019-07-06 11:08:09
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/accountsReceivableDetail.html" */ ?>
<?php
/*%%SmartyHeaderCode:13147773135d208119d09ca7_07186674%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f411b6646edaac3159aa557e7ba6993b65ec75b' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/accountsReceivableDetail.html',
      1 => 1560675141,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13147773135d208119d09ca7_07186674',
  'variables' => 
  array (
    'strTitle' => 0,
    'strSync' => 0,
    'strAccountTitle' => 0,
    'strTarget' => 0,
    'strValue' => 0,
    'strUnit' => 0,
    'strMemo' => 0,
    'strIdAccountTitle' => 0,
    'strIdSubAccountTitle' => 0,
    'strName' => 0,
    'strAddress' => 0,
    'arrRows' => 0,
    'value' => 0,
    'strSum' => 0,
    'strCautionMark' => 0,
    'strCaution1' => 0,
    'strCaution2' => 0,
    'strCaution3' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d208119df7e83_80690798',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d208119df7e83_80690798')) {
function content_5d208119df7e83_80690798 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '13147773135d208119d09ca7_07186674';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumn"  style="width:750px;" colspan=5><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strSync']->value;?>
</td>
		</tr>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:120px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strAccountTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:410px;" colspan=2><?php echo $_smarty_tpl->tpl_vars['strTarget']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:100px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strValue']->value;?>
<br><?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:120px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strMemo']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strIdAccountTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strIdSubAccountTitle']->value;?>
</td>
		</tr>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:180px;"><?php echo $_smarty_tpl->tpl_vars['strName']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:230px;"><?php echo $_smarty_tpl->tpl_vars['strAddress']->value;?>
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
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextName<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextName<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextName<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}SelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}SelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueSelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}SelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}SelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueSelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
			</tr>
		<?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle" ><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextSumOffset" ><div id="#{idSelf}TextSum" >#{valueTextSum}</div></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=7>
				<p><?php echo $_smarty_tpl->tpl_vars['strCautionMark']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution1']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution2']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution3']->value;?>
</p>
			</td>
		</tr>
	</tbody>
</table>


<?php }
}
?>