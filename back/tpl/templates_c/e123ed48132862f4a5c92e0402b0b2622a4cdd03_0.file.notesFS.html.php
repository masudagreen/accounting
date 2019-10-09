<?php /* Smarty version 3.1.24, created on 2016-08-21 16:57:33
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/notesFS.html" */ ?>
<?php
/*%%SmartyHeaderCode:156894501357b9dd7d4044f3_26962411%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e123ed48132862f4a5c92e0402b0b2622a4cdd03' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/notesFS.html',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '156894501357b9dd7d4044f3_26962411',
  'variables' => 
  array (
    'strComment' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b9dd7d513787_30258274',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b9dd7d513787_30258274')) {
function content_57b9dd7d513787_30258274 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '156894501357b9dd7d4044f3_26962411';
?>
<div style="margin:10px;">
<?php echo $_smarty_tpl->tpl_vars['strComment']->value;?>

</div><?php }
}
?>