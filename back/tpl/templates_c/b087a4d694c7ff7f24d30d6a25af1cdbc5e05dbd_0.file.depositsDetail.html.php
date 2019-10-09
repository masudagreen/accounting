<?php /* Smarty version 3.1.24, created on 2019-07-06 11:07:52
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/depositsDetail.html" */ ?>
<?php
/*%%SmartyHeaderCode:6954821365d208108be7405_84879944%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b087a4d694c7ff7f24d30d6a25af1cdbc5e05dbd' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/depositsDetail.html',
      1 => 1560675141,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6954821365d208108be7405_84879944',
  'variables' => 
  array (
    'strTitle' => 0,
    'strSync' => 0,
    'strBank' => 0,
    'strUnitBank' => 0,
    'strType' => 0,
    'strAccount' => 0,
    'strValue' => 0,
    'strUnit' => 0,
    'strMemo' => 0,
    'strIdAccountTitle' => 0,
    'strIdSubAccountTitle' => 0,
    'arrRows' => 0,
    'value' => 0,
    'strSeparate' => 0,
    'strSum' => 0,
    'strCautionMark' => 0,
    'strCaution1' => 0,
    'strCaution2' => 0,
    'strCaution3' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d208108d280a8_17978600',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d208108d280a8_17978600')) {
function content_5d208108d280a8_17978600 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '6954821365d208108be7405_84879944';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumn"  style="width:760px;" colspan=7><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strSync']->value;?>
</td>
		</tr>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle" style="width:180px;" colspan=3 ><?php echo $_smarty_tpl->tpl_vars['strBank']->value;?>
<br><?php echo $_smarty_tpl->tpl_vars['strUnitBank']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:100px;"><?php echo $_smarty_tpl->tpl_vars['strType']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:110px;"><?php echo $_smarty_tpl->tpl_vars['strAccount']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:150px;"><?php echo $_smarty_tpl->tpl_vars['strValue']->value;?>
<br><?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:220px;"><?php echo $_smarty_tpl->tpl_vars['strMemo']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" ><?php echo $_smarty_tpl->tpl_vars['strIdAccountTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" ><?php echo $_smarty_tpl->tpl_vars['strIdSubAccountTitle']->value;?>
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
				<td class="codePluginAccountingLibTableRowStr"  style="width:85px;" id="#{idSelf}TextBank<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextBank<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextBank<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr"  style="width:10px;"><?php echo $_smarty_tpl->tpl_vars['strSeparate']->value;?>
</td>
				<td class="codePluginAccountingLibTableRowStr"  style="width:85px; "id="#{idSelf}TextBranch<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextBranch<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextBranch<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextType<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextType<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextType<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextAccount<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextAccount<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextAccount<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
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
			<td class="codePluginAccountingLibTableColumnMiddle" colspan=3><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextSumOffset" ><div id="#{idSelf}TextSum" >#{valueTextSum}</div></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
			<td class="codePluginAccountingLibTableColumn" ></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=9>
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