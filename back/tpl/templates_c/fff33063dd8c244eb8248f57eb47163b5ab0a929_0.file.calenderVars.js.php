<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:04
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js" */ ?>
<?php
/*%%SmartyHeaderCode:64416800557b806fc622d40_71874150%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fff33063dd8c244eb8248f57eb47163b5ab0a929' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '64416800557b806fc622d40_71874150',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b806fc63c912_10328537',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b806fc63c912_10328537')) {
function content_57b806fc63c912_10328537 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '64416800557b806fc622d40_71874150';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_CalenderVars = Class.create({

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	iniLoad : function()
	{
		return this.varsLoad;
	}

});

<?php }
}
?>