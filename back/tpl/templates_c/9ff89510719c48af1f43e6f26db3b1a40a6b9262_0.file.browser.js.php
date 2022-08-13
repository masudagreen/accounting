<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:37
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/browser.js" */ ?>
<?php
/*%%SmartyHeaderCode:178981053562f6ef09dfa702_35823282%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9ff89510719c48af1f43e6f26db3b1a40a6b9262' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/browser.js',
      1 => 1329210284,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '178981053562f6ef09dfa702_35823282',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef09e01785_51762456',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef09e01785_51762456')) {
function content_62f6ef09e01785_51762456 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '178981053562f6ef09dfa702_35823282';
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