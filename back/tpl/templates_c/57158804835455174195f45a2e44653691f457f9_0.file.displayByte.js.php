<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:35
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js" */ ?>
<?php
/*%%SmartyHeaderCode:20763309715d99891b6947e5_72942907%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '57158804835455174195f45a2e44653691f457f9' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20763309715d99891b6947e5_72942907',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891b6e31c5_21499077',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891b6e31c5_21499077')) {
function content_5d99891b6e31c5_21499077 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '20763309715d99891b6947e5_72942907';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayByte = Class.create(
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 {
		num      : 0,
		flagFrom : 'b',
		flagTo   : 'mb'
	 }
	*/

	get : function(obj)
	{
		if(obj.flagFrom == 'b') {
			if(obj.flagTo == 'b') return obj.num;
			else if(obj.flagTo == 'kb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024/1024);
		}
		else if(obj.flagFrom == 'kb') {
			if(obj.flagTo == 'b') return obj.num*1024;
			else if(obj.flagTo == 'kb') return obj.num;
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024);
		}
		else if(obj.flagFrom == 'mb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024;
			else if(obj.flagTo == 'mb') return obj.num;
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024);
		}
		else if(obj.flagFrom == 'gb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024*1024;
			else if(obj.flagTo == 'mb') return obj.num*1024;
			else if(obj.flagTo == 'gb') return obj.num;
		}
	}
});
<?php }
}
?>