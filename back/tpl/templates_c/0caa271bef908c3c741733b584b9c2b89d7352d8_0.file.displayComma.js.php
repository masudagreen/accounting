<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/displayComma.js" */ ?>
<?php
/*%%SmartyHeaderCode:163365363862f6ef0a170865_75324782%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0caa271bef908c3c741733b584b9c2b89d7352d8' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/displayComma.js',
      1 => 1340540054,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '163365363862f6ef0a170865_75324782',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a176296_70182928',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a176296_70182928')) {
function content_62f6ef0a176296_70182928 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '163365363862f6ef0a170865_75324782';
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