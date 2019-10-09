<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayComma.js" */ ?>
<?php
/*%%SmartyHeaderCode:161372356657b807056261d0_70538256%%*/
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
  'nocache_hash' => '161372356657b807056261d0_70538256',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b8070562fc60_12728048',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b8070562fc60_12728048')) {
function content_57b8070562fc60_12728048 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '161372356657b807056261d0_70538256';
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