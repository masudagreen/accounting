<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formFocus.js" */ ?>
<?php
/*%%SmartyHeaderCode:14731353595d06058fee6932_26909247%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '24ae6904822739770710f131106429de24e1eae8' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formFocus.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14731353595d06058fee6932_26909247',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fee9ef2_43027812',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fee9ef2_43027812')) {
function content_5d06058fee9ef2_43027812 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '14731353595d06058fee6932_26909247';
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