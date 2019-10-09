<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/css.js" */ ?>
<?php
/*%%SmartyHeaderCode:44320314357b5af0dd2ede8_88753000%%*/
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
  'nocache_hash' => '44320314357b5af0dd2ede8_88753000',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0dd477f6_72822783',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0dd477f6_72822783')) {
function content_57b5af0dd477f6_72822783 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '44320314357b5af0dd2ede8_88753000';
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