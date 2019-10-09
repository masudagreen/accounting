<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js" */ ?>
<?php
/*%%SmartyHeaderCode:3816656015d06058fd6be13_92314375%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fff33063dd8c244eb8248f57eb47163b5ab0a929' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3816656015d06058fd6be13_92314375',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fd77d45_11515602',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fd77d45_11515602')) {
function content_5d06058fd77d45_11515602 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '3816656015d06058fd6be13_92314375';
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