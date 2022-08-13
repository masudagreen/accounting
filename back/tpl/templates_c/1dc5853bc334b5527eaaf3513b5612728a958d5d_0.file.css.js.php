<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/css.js" */ ?>
<?php
/*%%SmartyHeaderCode:151525546862f6ef0a159c49_57505606%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1dc5853bc334b5527eaaf3513b5612728a958d5d' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/css.js',
      1 => 1329210250,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '151525546862f6ef0a159c49_57505606',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a15cbe9_01135458',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a15cbe9_01135458')) {
function content_62f6ef0a15cbe9_01135458 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '151525546862f6ef0a159c49_57505606';
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