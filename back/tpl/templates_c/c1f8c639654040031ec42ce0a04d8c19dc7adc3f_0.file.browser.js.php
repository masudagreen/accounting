<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/browser.js" */ ?>
<?php
/*%%SmartyHeaderCode:10725354195d06058fca93a9_93774489%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1f8c639654040031ec42ce0a04d8c19dc7adc3f' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/browser.js',
      1 => 1560675138,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10725354195d06058fca93a9_93774489',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fcaf880_54737469',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fcaf880_54737469')) {
function content_5d06058fcaf880_54737469 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10725354195d06058fca93a9_93774489';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Browser = Class.create(Code_Lib_Ext, {


	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
		if (this._checkBrowser()) return 1;
		return 0;
	},

	/**
	 *
	*/
	insRoot : null, idSelf : null,
	_iniVars : function(obj)
	{
		this.insRoot = obj.insRoot;
		this.idSelf = obj.idSelf;
	},

	/**
	 *
	*/
	_checkBrowser : function()
	{
		var userAgent = window.navigator.userAgent.toLowerCase();
		var appVersion = window.navigator.appVersion.toLowerCase();
		var ieModern = (typeof document.documentElement.style.msInterpolationMode != "undefined")? 1 : 0;

		if (userAgent.indexOf("opera") > -1) {
			alert(this.varsLoad.strNone);
			return 1;

		} else if (userAgent.indexOf("msie") > -1) {
			if (appVersion.indexOf("msie 7.0") > -1 || !ieModern) {
				alert(this.varsLoad.strNone);
				return 1;
			}
		}
	}

});
<?php }
}
?>