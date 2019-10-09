<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:35
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/css.js" */ ?>
<?php
/*%%SmartyHeaderCode:12612002535d99891b5af2d0_16411283%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fc8b4693454f3038bee7a20a695d7da1b3bca366' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/css.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12612002535d99891b5af2d0_16411283',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891b5cae79_27280198',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891b5cae79_27280198')) {
function content_5d99891b5cae79_27280198 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12612002535d99891b5af2d0_16411283';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Css = Class.create({

	/**
	 * obj = {
	 * 	path : string,
	 * }
	*/
	initialize : function(obj)
	{
		var head;
		$$('head').each(function(ele)
		{
			head = ele;
		});
		var link = document.createElement('link');
		link.href = obj.path;
		link.rel = 'stylesheet';
		link.type = 'text/css';

		var array = $$('link');
		for(var i = 0; i < array.length; i++) {
			if(array[i].href == link.href) return;
		}
		head.insert(link);
	}

});
<?php }
}
?>