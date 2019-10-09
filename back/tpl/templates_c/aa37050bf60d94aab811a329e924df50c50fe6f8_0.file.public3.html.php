<?php /* Smarty version 3.1.24, created on 2016-08-20 07:35:01
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public3.html" */ ?>
<?php
/*%%SmartyHeaderCode:50846974157b80825684417_37886413%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa37050bf60d94aab811a329e924df50c50fe6f8' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/2012/summaryStatement/public3.html',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '50846974157b80825684417_37886413',
  'variables' => 
  array (
    'strTitle' => 0,
    'strTitle1' => 0,
    'strDirector' => 0,
    'strSum' => 0,
    'strSumFamily' => 0,
    'strSumWork' => 0,
    'strTitle2' => 0,
    'strSelectSalaryFixed' => 0,
    'strSelectPercent' => 0,
    'strSelectHybrid' => 0,
    'strTitle3' => 0,
    'strSelectHouseCheck' => 0,
    'strSelectHouseNone' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b808257f21e1_76548296',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b808257f21e1_76548296')) {
function content_57b808257f21e1_76548296 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '50846974157b80825684417_37886413';
?>
<table style="" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn"style="font-weight: bold;" colspan=3><?php echo $_smarty_tpl->tpl_vars['strTitle']->value;?>
</td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" rowspan=8><?php echo $_smarty_tpl->tpl_vars['strTitle1']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strDirector']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}DirectorOffset" style="width:750px;"><div id="#{idSelf}Director"  >#{valueDirector}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text1Offset"><div id="#{idSelf}Text1"  >#{valueText1}</div></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}Num1Offset"><div id="#{idSelf}Num1"  >#{valueNum1}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text2Offset"><div id="#{idSelf}Text2"  >#{valueText2}</div></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}Num2Offset"><div id="#{idSelf}Num2"  >#{valueNum2}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text3Offset"><div id="#{idSelf}Text3"  >#{valueText3}</div></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}Num3Offset"><div id="#{idSelf}Num3"  >#{valueNum3}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableRowStr" id="#{idSelf}Text4Offset"><div id="#{idSelf}Text4"  >#{valueText4}</div></td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}Num4Offset"><div id="#{idSelf}Num4"  >#{valueNum4}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSum']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SumOffset"><div id="#{idSelf}Sum"  >#{valueSum}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSumFamily']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SumFamilyOffset"><div id="#{idSelf}SumFamily"  >#{valueSumFamily}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSumWork']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SumWorkOffset"><div id="#{idSelf}SumWork"  >#{valueSumWork}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" rowspan=3><?php echo $_smarty_tpl->tpl_vars['strTitle2']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSelectSalaryFixed']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SelectSalaryFixedOffset"><div id="#{idSelf}SelectSalaryFixed"  >#{valueSelectSalaryFixed}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSelectPercent']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SelectPercentOffset"><div id="#{idSelf}SelectPercent"  >#{valueSelectPercent}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSelectHybrid']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SelectHybridOffset"><div id="#{idSelf}SelectHybrid"  >#{valueSelectHybrid}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" rowspan=2><?php echo $_smarty_tpl->tpl_vars['strTitle3']->value;?>
</td>
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSelectHouseCheck']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SelectHouseCheckOffset"><div id="#{idSelf}SelectHouseCheck"  >#{valueSelectHouseCheck}</div></td>
		</tr>
		<tr valign="top">
			<td class="codePluginAccountingLibTableColumn" ><?php echo $_smarty_tpl->tpl_vars['strSelectHouseNone']->value;?>
</td>
			<td class="codePluginAccountingLibTableRowStrRight" id="#{idSelf}SelectHouseNoneOffset"><div id="#{idSelf}SelectHouseNone"  >#{valueSelectHouseNone}</div></td>
		</tr>
	</tbody>
</table><?php }
}
?>