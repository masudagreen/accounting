<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/css.js" */ ?>
<?php
/*%%SmartyHeaderCode:197235909457b807055e1820_14993654%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '404b1c7b11a972327fc02a26b2b723d40250bf49' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/css.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '197235909457b807055e1820_14993654',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807055ea3f5_97295402',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807055ea3f5_97295402')) {
function content_57b807055ea3f5_97295402 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '197235909457b807055e1820_14993654';
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