<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js" */ ?>
<?php
/*%%SmartyHeaderCode:8935424815d06058fe47200_76918714%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfcc58830e52ff7564fe2769eb463a790e981b34' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8935424815d06058fe47200_76918714',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fe4a550_38690443',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fe4a550_38690443')) {
function content_5d06058fe4a550_38690443 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '8935424815d06058fe47200_76918714';
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