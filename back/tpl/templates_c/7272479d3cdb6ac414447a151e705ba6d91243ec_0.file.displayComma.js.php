<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:35
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js" */ ?>
<?php
/*%%SmartyHeaderCode:18308568995d99891b779010_22216887%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7272479d3cdb6ac414447a151e705ba6d91243ec' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18308568995d99891b779010_22216887',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891b7c0c80_09074310',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891b7c0c80_09074310')) {
function content_5d99891b7c0c80_09074310 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '18308568995d99891b779010_22216887';
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