<?php /* Smarty version 3.1.24, created on 2016-08-20 07:34:16
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public0.html" */ ?>
<?php
/*%%SmartyHeaderCode:194992517357b807f8838b57_74085682%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '81cd220ede0e1f4d81538bf97b72a6e94675a015' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public0.html',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194992517357b807f8838b57_74085682',
  'variables' => 
  array (
    'strId' => 0,
    'strCompany' => 0,
    'strStoreName' => 0,
    'strNum' => 0,
    'strStartTerm' => 0,
    'strEndTerm' => 0,
    'strTaxPay' => 0,
    'strMail' => 0,
    'strZipCode' => 0,
    'strPhone' => 0,
    'strHomeUrl' => 0,
    'strName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807f8990f45_38312816',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807f8990f45_38312816')) {
function content_57b807f8990f45_38312816 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '194992517357b807f8838b57_74085682';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strId']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}IdOffset" style="width:750px;"><div id="#{idSelf}Id" >#{valueId}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strCompany']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}CompanyOffset"><div id="#{idSelf}Company" >#{valueCompany}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strStoreName']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}StoreNameOffset"><div id="#{idSelf}StoreName" >#{valueStoreName}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strNum']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strStartTerm']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}StartTermOffset"><div id="#{idSelf}StartTerm" >#{valueStartTerm}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strEndTerm']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}EndTermOffset"><div id="#{idSelf}EndTerm" >#{valueEndTerm}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strTaxPay']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}TaxPayOffset"><div id="#{idSelf}TaxPay" >#{valueTaxPay}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strMail']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strZipCode']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}ZipCodeOffset" colspan=2><div id="#{idSelf}ZipCode" >#{valueZipCode}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strPhone']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}PhoneOffset"><div id="#{idSelf}Phone" >#{valuePhone}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strHomeUrl']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}HomeUrlOffset"><div id="#{idSelf}HomeUrl" >#{valueHomeUrl}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" colspan=2><?php echo $_smarty_tpl->tpl_vars['strName']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}NameOffset"><div id="#{idSelf}Name" >#{valueName}</div></td>
		</tr>
	</tbody>
</table><?php }
}
?>