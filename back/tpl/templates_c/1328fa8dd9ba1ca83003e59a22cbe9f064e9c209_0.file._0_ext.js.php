<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_0_ext.js" */ ?>
<?php
/*%%SmartyHeaderCode:108273052957b5af0d2a35d2_83421411%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1328fa8dd9ba1ca83003e59a22cbe9f064e9c209' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_0_ext.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '108273052957b5af0d2a35d2_83421411',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d2b9649_89514842',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d2b9649_89514842')) {
function content_57b5af0d2b9649_89514842 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '108273052957b5af0d2a35d2_83421411';
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