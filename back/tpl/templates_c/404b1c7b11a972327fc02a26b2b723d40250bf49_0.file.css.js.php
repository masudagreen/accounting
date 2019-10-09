<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/css.js" */ ?>
<?php
/*%%SmartyHeaderCode:1317192555d06058fe22016_07929027%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '404b1c7b11a972327fc02a26b2b723d40250bf49' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/css.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1317192555d06058fe22016_07929027',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fe25583_35763370',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fe25583_35763370')) {
function content_5d06058fe25583_35763370 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1317192555d06058fe22016_07929027';
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