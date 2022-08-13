<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:37
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/calenderVars.js" */ ?>
<?php
/*%%SmartyHeaderCode:201435463262f6ef09f35865_19699146%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5065dc9e5b7af623bf9dd4b92ee28dd5fb539f48' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/calenderVars.js',
      1 => 1329210278,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201435463262f6ef09f35865_19699146',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef09f3eae4_49857104',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef09f3eae4_49857104')) {
function content_62f6ef09f3eae4_49857104 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '201435463262f6ef09f35865_19699146';
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