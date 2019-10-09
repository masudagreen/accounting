<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:34
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js" */ ?>
<?php
/*%%SmartyHeaderCode:5937961735d99891a567aa6_33265709%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db8174f1f416fc83342a3572b98c1c6df68e1f6f' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/calenderVars.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5937961735d99891a567aa6_33265709',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891a586b41_08906867',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891a586b41_08906867')) {
function content_5d99891a586b41_08906867 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5937961735d99891a567aa6_33265709';
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