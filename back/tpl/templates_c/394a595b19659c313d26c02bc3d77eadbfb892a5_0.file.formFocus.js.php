<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/formFocus.js" */ ?>
<?php
/*%%SmartyHeaderCode:203639509262f6ef0a254400_88543416%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '394a595b19659c313d26c02bc3d77eadbfb892a5' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/formFocus.js',
      1 => 1329210314,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '203639509262f6ef0a254400_88543416',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a258fa8_70959316',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a258fa8_70959316')) {
function content_62f6ef0a258fa8_70959316 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '203639509262f6ef0a254400_88543416';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormFocus = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniTemplate();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 * Template
	*/
	_iniTemplate : function()
	{
		this._setTemplateListener();
	},

	/**
	 *
	*/
	_setTemplateListener : function()
	{
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'focus',
		strFunc : '_focusCheck',	ele : this.eleInsert, vars : '' });
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
		strFunc : '_blurCheck', ele : this.eleInsert, vars : '' });
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.stopListener();
	},

	/**
	 *
	*/
	_focusCheck : function(obj, evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.allot({
			from       : '_focusCheck',
			insCurrent : this.insCurrent
		});
	},

	/**
	 *
	*/
	_blurCheck : function(obj, evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		var value = this.allot({
			from       : '_blurCheck',
			insCurrent : this.insCurrent,
			vars       : this.eleInsert.value
		});
		this.eleInsert.value = value;
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	}
});

<?php }
}
?>