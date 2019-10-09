<?php /* Smarty version 3.1.24, created on 2019-10-06 10:05:04
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceItem.html" */ ?>
<?php
/*%%SmartyHeaderCode:797665725d99bc507c3e32_61429499%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5efa573f1a73acbf1a7afde3223191ef469ff31' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceItem.html',
      1 => 1570328742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '797665725d99bc507c3e32_61429499',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99bc507e7128_90080703',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99bc507e7128_90080703')) {
function content_5d99bc507e7128_90080703 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '797665725d99bc507c3e32_61429499';
?>
<tr>
	<td class="codeLibBaseTableRow #{strClassStrTitle}">#{strTitle}</td>
	<td class="codeLibBaseTableRow #{strClassNumValue}" id="#{idSelf}#{id}Offset"><div id="#{idSelf}#{id}" style="height:16px;text-align:right;">#{numValue}</div></td>
</tr><?php }
}
?>