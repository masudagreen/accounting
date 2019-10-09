<?php /* Smarty version 3.1.24, created on 2019-10-06 10:05:04
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceSubItem.html" */ ?>
<?php
/*%%SmartyHeaderCode:18129807935d99bc508fe602_79319952%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e81d8b27f06c43dbc9a45ea313eeeae200a5acae' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/html/balanceSubItem.html',
      1 => 1570328742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18129807935d99bc508fe602_79319952',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99bc50920c94_13246443',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99bc50920c94_13246443')) {
function content_5d99bc50920c94_13246443 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '18129807935d99bc508fe602_79319952';
?>
<tr>
	<td class="codeLibBaseTableRow #{strClassStrTitle}">#{strTitle}</td>
	<td class="codeLibBaseTableRow #{strClassNumValue}" id="#{idSelf}#{id}Offset"><div id="#{idSelf}#{id}" style="height:16px;text-align:right;">#{numValue}</div></td>
</tr><?php }
}
?>