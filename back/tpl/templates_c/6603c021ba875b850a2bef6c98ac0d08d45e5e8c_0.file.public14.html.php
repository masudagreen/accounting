<?php /* Smarty version 3.1.24, created on 2019-08-08 15:12:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public14.html" */ ?>
<?php
/*%%SmartyHeaderCode:9019064985d4c3bd634a984_83287755%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6603c021ba875b850a2bef6c98ac0d08d45e5e8c' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public14.html',
      1 => 1560675142,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9019064985d4c3bd634a984_83287755',
  'variables' => 
  array (
    'strTitle' => 0,
    'strSheet' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d4c3bd63fa744_47000582',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d4c3bd63fa744_47000582')) {
function content_5d4c3bd63fa744_47000582 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '9019064985d4c3bd634a984_83287755';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn"style="font-weight: bold;" colspan=3><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" rowspan=14><?php echo $_smarty_tpl->tpl_vars['strSheet']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text1Offset" style="width:335px;"><div id="#{idSelf}Text1"  >#{valueText1}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text2Offset" style="width:335px;"><div id="#{idSelf}Text2"  >#{valueText2}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text3Offset" ><div id="#{idSelf}Text3"  >#{valueText3}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text4Offset" ><div id="#{idSelf}Text4"  >#{valueText4}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text5Offset" ><div id="#{idSelf}Text5"  >#{valueText5}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text6Offset" ><div id="#{idSelf}Text6"  >#{valueText6}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text7Offset" ><div id="#{idSelf}Text7"  >#{valueText7}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text8Offset" ><div id="#{idSelf}Text8"  >#{valueText8}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text9Offset" ><div id="#{idSelf}Text9"  >#{valueText9}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text10Offset" ><div id="#{idSelf}Text10"  >#{valueText10}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text11Offset" ><div id="#{idSelf}Text11"  >#{valueText11}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text12Offset" ><div id="#{idSelf}Text12"  >#{valueText12}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text13Offset" ><div id="#{idSelf}Text13"  >#{valueText13}</div></td>
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text14Offset" ><div id="#{idSelf}Text14"  >#{valueText14}</div></td>
		</tr>
	</tbody>
</table><?php }
}
?>