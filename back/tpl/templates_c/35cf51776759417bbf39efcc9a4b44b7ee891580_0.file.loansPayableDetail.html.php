<?php /* Smarty version 3.1.24, created on 2016-08-18 13:49:39
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/loansPayableDetail.html" */ ?>
<?php
/*%%SmartyHeaderCode:211762266457b5bcf35650e8_45921946%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35cf51776759417bbf39efcc9a4b44b7ee891580' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/detailedAccount/loansPayableDetail.html',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '211762266457b5bcf35650e8_45921946',
  'variables' => 
  array (
    'strTitle' => 0,
    'strSync' => 0,
    'strBorrower' => 0,
    'strRelation' => 0,
    'strValue' => 0,
    'strUnit' => 0,
    'strValuePayable' => 0,
    'strReason' => 0,
    'strMemo' => 0,
    'strIdAccountTitle' => 0,
    'strIdSubAccountTitle' => 0,
    'strAddress' => 0,
    'strRate' => 0,
    'arrRows' => 0,
    'value' => 0,
    'strSum' => 0,
    'strBlank' => 0,
    'strCautionMark' => 0,
    'strCaution1' => 0,
    'strCaution2' => 0,
    'strCaution3' => 0,
    'strCaution4' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5bcf37ab206_78451994',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5bcf37ab206_78451994')) {
function content_57b5bcf37ab206_78451994 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '211762266457b5bcf35650e8_45921946';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=6><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strSync']->value;?>
</td>
		</tr>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:160px;"><?php echo $_smarty_tpl->tpl_vars['strBorrower']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:60px;font-size:4px;"><?php echo $_smarty_tpl->tpl_vars['strRelation']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:125px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strValue']->value;?>
<br><?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:115px;font-size:8px;"><?php echo $_smarty_tpl->tpl_vars['strValuePayable']->value;?>
<br><?php echo $_smarty_tpl->tpl_vars['strUnit']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:120px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strReason']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle"  style="width:170px;" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strMemo']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strIdAccountTitle']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strIdSubAccountTitle']->value;?>
</td>
		</tr>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle" colspan=2><?php echo $_smarty_tpl->tpl_vars['strAddress']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumnMiddle" ><?php echo $_smarty_tpl->tpl_vars['strRate']->value;?>
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
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextBorrower<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextBorrower<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextBorrower<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextRelation<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextRelation<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextRelation<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>

				<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" rowspan=2><div id="#{idSelf}TextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextValuePayable<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextValuePayable<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextValuePayable<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>

				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextReason<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" rowspan=2><div id="#{idSelf}TextReason<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextReason<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" rowspan=2><div id="#{idSelf}TextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextMemo<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}SelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" rowspan=2><div id="#{idSelf}SelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueSelectIdAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}SelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" rowspan=2><div id="#{idSelf}SelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueSelectIdSubAccountTitle<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
			</tr>
			<tr valign="top">
				<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}TextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset" colspan=2><div id="#{idSelf}TextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextAddress<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
				<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextRate<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
Offset"><div id="#{idSelf}TextRate<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" >#{valueTextRate<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
}</div></td>
			</tr>
		<?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
		<tr valign="middle">
			<td class="codePluginAccountingLibTableColumnMiddle" colspan=2 rowspan=2><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextSumOffset" rowspan=2><div id="#{idSelf}TextSum" >#{valueTextSum}</div></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TextSumPayableOffset" ><div id="#{idSelf}TextSumPayable" >#{valueTextSumPayable}</div></td>
			<td class="codePluginAccountingLibTableColumn" rowspan=2></td>
			<td class="codePluginAccountingLibTableColumn" rowspan=2></td>
			<td class="codePluginAccountingLibTableColumn" rowspan=2></td>
			<td class="codePluginAccountingLibTableColumn" rowspan=2></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strBlank']->value;?>
</td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=8>
				<p><?php echo $_smarty_tpl->tpl_vars['strCautionMark']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution1']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution2']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution3']->value;?>
</p>
				<p><?php echo $_smarty_tpl->tpl_vars['strCaution4']->value;?>
</p>
			</td>
		</tr>
	</tbody>
</table>


<?php }
}
?>