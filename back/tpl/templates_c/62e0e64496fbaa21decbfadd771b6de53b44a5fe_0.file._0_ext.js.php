<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:37
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_0_ext.js" */ ?>
<?php
/*%%SmartyHeaderCode:24970687462f6ef09c9a8c0_08372031%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '62e0e64496fbaa21decbfadd771b6de53b44a5fe' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_0_ext.js',
      1 => 1329210248,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24970687462f6ef09c9a8c0_08372031',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef09ca76b2_35090526',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef09ca76b2_35090526')) {
function content_62f6ef09ca76b2_35090526 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '24970687462f6ef09c9a8c0_08372031';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Ext = Class.create({

	/**
	 * Request
	*/
	insRequest  : null,
	_extRequest : function ()
	{
		this.insRequest = new Code_Lib_Request({insRoot: this.insRoot});
	},

	/**
	 * obj = {
	 * 	id       : string,
	 * 	pathSelf : string,
	 * }
	*/
	insCake : null,
	_extCake : function(obj)
	{
		this.insCake = new Code_Lib_Cake({
			insRoot  : this.insSelf,
			idSelf   : (obj.id)? 'Cake' + obj.id : 'Cake',
			pathSelf : obj.pathSelf
		});
	},

	/**
	 * Browser
	*/
	insBrowser : null,
	_extBrowser : function () {
		this.insBrowser = new Code_Lib_Browser();
		var flag = this.insBrowser.iniLoad({
			insRoot  : this.insSelf,
			idSelf   : 'Browser'
		});

		return flag;
	},

	/**
	 *
	*/
	insSelf : null, insRoot : null,
	_extVars : function ()
	{
		this.insSelf = this;
		this.insRoot = this;
	},

	/**
	 * Listener
	*/
	insListener : null,
	_extListener : function()
	{
		this.insListener = new Code_Lib_Listener();
	},

	/**
	 * Key
	*/
	_extKey : function()
	{
		this._setKeyLisener();
	},

	/**
	 *
	*/
	_setKeyLisener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'keydown',
			strFunc : '_keydownKey', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_keydownKey : function(evt)
	{
		this._resetLogout();
		if (evt.keyCode == 13) {
			if (!evt.findElement('textarea')) {
				evt.stop();
			}
		}
	},

	/**
	 *
	*/
	setZIndex : function()
	{
		this._resetLogout();
		this.vars.varsSystem.num.zIndex++;

		return this.vars.varsSystem.num.zIndex;
	},

	/**
	 *
	*/
	getZIndex : function()
	{
		return this.vars.varsSystem.num.zIndex;
	},

	/**
	 *
	*/
	_varsLogout : null,
	_resetLogout : function()
	{

	},

	/**
	 * obj = {
	 * 	numTimeZone : int,
	 * }
	*/
	_extTimeZone : function(obj)
	{
		this.insTimeZone = new Code_Lib_TimeZone({
			numTimeZone : obj.numTimeZone
		});
	},

	/**
	 * obj = {
	 * 	eleInsert : ele,
	 * 	vars  : object,
	 * }
	*/
	insWindow  : null,
	_extWindow : function (obj)
	{
		this.insWindow = new Code_Lib_Window();
		this.insWindow.iniLoad({
			eleInsert  : obj.eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Window',
			allot      : this._getWindowAllot(),
			vars       : obj.vars
		});

	},

	/**
	 * obj = {
	 * 	vars  : obj,
	 * }
	*/
	insLayout : null,
	_extLayout : function(obj)
	{
		this.insLayout = new Code_Lib_TemplateLayout({
			eleInsert  : $(this.insWindow.idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0),
			idWindow   : this.insWindow.idWindow,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Layout',
			allot      : function(){},
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_varsValue : null,
	_eventValue : function(obj)
	{
		this._varsValue = {
			vars     : (obj.vars)? obj.vars : '',
			idTarget : (obj.idTarget)? obj.idTarget : ''
		};
	}

});

<?php }
}
?>