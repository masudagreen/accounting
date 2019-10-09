<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js" */ ?>
<?php
/*%%SmartyHeaderCode:35231500057b5af0dda2d56_49145823%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfcc58830e52ff7564fe2769eb463a790e981b34' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '35231500057b5af0dda2d56_49145823',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0ddc9108_32848713',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0ddc9108_32848713')) {
function content_57b5af0ddc9108_32848713 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '35231500057b5af0dda2d56_49145823';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayComma = Class.create({

	/**
	 * obj = {
	 * 	num : int,
	 * }
	*/
	get : function(obj)
	{
		var num = obj.num;
		if(num < 1000 && num > -1000) return num;

		var str = "";
		if (num <= -1000) {
			num *= -1;
			while (num != (str = new String(num).replace(/^(\d+)(\d\d\d)/,"$1,$2"))) {
				num = str;
			}
			num = '-' + num;

		} else {
			while (num != (str = new String(num).replace(/^(\d+)(\d\d\d)/,"$1,$2"))) {
				num = str;
			}
		}

		return num;
	}
});
<?php }
}
?>